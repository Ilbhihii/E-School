<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NotificationCenterService
{
    /**
     * Envoie une notification persistante dans le centre de notifications.
     * Si le service FCM du projet est disponible, un push est également tenté.
     */
    public function send(
        User $recipient,
        string $title,
        string $message,
        string $category = 'general',
        ?string $url = null,
        string $icon = 'bi bi-bell-fill',
        array $extra = [],
        bool $push = true,
        string $priority = 'normal'
    ): void {
        $recipient->notify(new GeneralNotification(
            $title,
            $message,
            $category,
            $url,
            $icon,
            $extra,
            $priority
        ));

        if ($push) {
            $this->sendPushIfAvailable(
                $recipient,
                $title,
                $message,
                $category,
                $url,
                $extra
            );
        }
    }

    /**
     * Envoie à une collection / liste d'utilisateurs sans doublon.
     */
    public function sendToUsers(
        iterable $users,
        string $title,
        string $message,
        string $category = 'general',
        ?string $url = null,
        string $icon = 'bi bi-bell-fill',
        array $extra = [],
        bool $push = true,
        string $priority = 'normal'
    ): int {
        $unique = collect($users)
            ->filter(fn ($user) => $user instanceof User)
            ->unique('id')
            ->values();

        foreach ($unique as $user) {
            $this->send(
                $user,
                $title,
                $message,
                $category,
                $url,
                $icon,
                $extra,
                $push,
                $priority
            );
        }

        return $unique->count();
    }

    /**
     * Envoie à tous les comptes actifs d'un rôle.
     */
    public function sendToRole(
        string $role,
        string $title,
        string $message,
        string $category = 'general',
        ?string $url = null,
        string $icon = 'bi bi-bell-fill',
        array $extra = [],
        bool $push = true,
        string $priority = 'normal'
    ): int {
        $users = User::query()
            ->where('role', $role)
            ->where('is_active', true)
            ->get();

        return $this->sendToUsers(
            $users,
            $title,
            $message,
            $category,
            $url,
            $icon,
            $extra,
            $push,
            $priority
        );
    }

    public function sendToAdmins(
        string $title,
        string $message,
        string $category = 'general',
        ?string $url = null,
        string $icon = 'bi bi-shield-fill-check',
        array $extra = [],
        bool $push = true,
        string $priority = 'normal'
    ): int {
        return $this->sendToRole(
            'admin',
            $title,
            $message,
            $category,
            $url,
            $icon,
            $extra,
            $push,
            $priority
        );
    }

    /**
     * Envoie à l'étudiant ET à ses parents.
     *
     * $parentPermission peut être :
     * - can_view_schedule
     * - can_view_absences
     * - can_view_assignments
     * - can_view_results
     *
     * Si la permission est fournie, seuls les parents autorisés reçoivent la notification.
     */
    public function sendToStudentAndParents(
        User $student,
        string $title,
        string $message,
        string $category = 'general',
        ?string $url = null,
        string $icon = 'bi bi-bell-fill',
        array $extra = [],
        ?string $parentPermission = null,
        ?string $parentTitle = null,
        ?string $parentMessage = null,
        ?string $parentUrl = null,
        bool $push = true,
        string $priority = 'normal'
    ): array {
        $this->send(
            $student,
            $title,
            $message,
            $category,
            $url,
            $icon,
            $extra,
            $push,
            $priority
        );

        $parentsSent = 0;

        if (!method_exists($student, 'parents')) {
            return ['student' => 1, 'parents' => 0];
        }

        $allowedPermissions = [
            'can_view_schedule',
            'can_view_absences',
            'can_view_assignments',
            'can_view_results',
        ];

        $parents = $student->parents()->get();

        foreach ($parents as $parent) {
            if ($parentPermission !== null) {
                if (!in_array($parentPermission, $allowedPermissions, true)) {
                    throw new \InvalidArgumentException(
                        'Permission parent inconnue : ' . $parentPermission
                    );
                }

                if (!(bool) data_get($parent->pivot, $parentPermission, false)) {
                    continue;
                }
            }

            $this->send(
                $parent,
                $parentTitle ?: $title,
                $parentMessage ?: $message,
                $category,
                $parentUrl ?: $url,
                $icon,
                array_merge($extra, [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                ]),
                $push,
                $priority
            );

            $parentsSent++;
        }

        return ['student' => 1, 'parents' => $parentsSent];
    }

    /**
     * Push Firebase optionnel : le centre web fonctionne même sans Firebase.
     */
    protected function sendPushIfAvailable(
        User $recipient,
        string $title,
        string $message,
        string $category,
        ?string $url,
        array $extra
    ): void {
        if (!class_exists(\App\Services\PushNotificationService::class)) {
            return;
        }

        if (!$this->pushAllowed($recipient, $category)) {
            return;
        }

        try {
            /** @var \App\Services\PushNotificationService $pushService */
            $pushService = app(\App\Services\PushNotificationService::class);

            $data = [
                'category' => (string) $category,
                'url' => (string) ($url ?? ''),
            ];

            foreach ($extra as $key => $value) {
                if (is_scalar($value) || $value === null) {
                    $data[(string) $key] = (string) ($value ?? '');
                }
            }

            $pushService->sendToUser($recipient, $title, $message, $data);
        } catch (\Throwable $e) {
            Log::warning('[NotificationCenter] Push ignoré : ' . $e->getMessage(), [
                'user_id' => $recipient->id,
                'category' => $category,
            ]);
        }
    }

    /**
     * Respecte les préférences push déjà présentes dans le projet.
     * La notification web en base reste toujours enregistrée.
     */
    protected function pushAllowed(User $user, string $category): bool
    {
        if (!method_exists($user, 'getOrCreateNotificationPreference')) {
            return true;
        }

        $preferenceMap = [
            'course' => 'new_courses',
            'live' => 'live_reminders',
            'appointment' => 'appointment_updates',
            'progress' => 'progress_updates',
            'result' => 'progress_updates',
            'vocal_test' => 'vocal_test_feedback',
            'promotion' => 'promotional',
        ];

        $field = $preferenceMap[$category] ?? null;

        if ($field === null) {
            return true;
        }

        try {
            $preferences = $user->getOrCreateNotificationPreference();
            return (bool) $preferences->{$field};
        } catch (\Throwable $e) {
            return true;
        }
    }
}
