<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class PushNotificationService
{
    protected ?Messaging $messaging = null;

    public function __construct()
    {
        // Tentative d'initialisation Firebase ; en cas d'échec
        // le service fonctionne en mode silencieux (utile en dev sans credentials)
        try {
            $this->messaging = app(Messaging::class);
        } catch (\Throwable $e) {
            Log::warning('Firebase non configuré : ' . $e->getMessage());
            $this->messaging = null;
        }
    }

    /**
     * Vérifie si Firebase est configuré
     */
    public function isConfigured(): bool
    {
        return $this->messaging !== null;
    }

    /**
     * Envoyer une notification à un token spécifique
     */
    public function sendToToken(
        string $token,
        string $title,
        string $body,
        array $data = []
    ): bool {
        if (!$this->isConfigured()) {
            Log::info('[PushNotification] Firebase non configuré. Notification ignorée.', [
                'title' => $title,
                'body' => $body,
            ]);
            return false;
        }

        try {
            $notification = Notification::create($title, $body);

            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data)
                ->withChangedTarget('token', $token);

            $this->messaging->send($message);
            return true;
        } catch (FirebaseException $e) {
            $this->handleFirebaseError($token, $e);
            return false;
        } catch (\Throwable $e) {
            Log::error('[PushNotification] Erreur inattendue : ' . $e->getMessage(), [
                'token' => substr($token, 0, 20) . '...',
            ]);
            return false;
        }
    }

    /**
     * Envoyer une notification à un utilisateur (tous ses tokens actifs)
     */
    public function sendToUser(
        User $user,
        string $title,
        string $body,
        array $data = []
    ): int {
        $tokens = $user->deviceTokens()->active()->pluck('token')->toArray();

        if (empty($tokens)) {
            return 0;
        }

        $sent = 0;
        foreach ($tokens as $token) {
            if ($this->sendToToken($token, $title, $body, $data)) {
                $sent++;
            }
        }

        return $sent;
    }

    /**
     * Envoyer une notification à plusieurs utilisateurs
     */
    public function sendToUsers(
        iterable $users,
        string $title,
        string $body,
        array $data = []
    ): int {
        $sent = 0;
        foreach ($users as $user) {
            $sent += $this->sendToUser($user, $title, $body, $data);
        }
        return $sent;
    }

    /**
     * Envoyer une notification à tous les tokens d'une plateforme spécifique
     */
    public function sendToPlatform(
        string $platform,
        string $title,
        string $body,
        array $data = []
    ): int {
        $tokens = DeviceToken::active()
            ->platform($platform)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return 0;
        }

        $sent = 0;
        foreach ($tokens as $token) {
            if ($this->sendToToken($token, $title, $body, $data)) {
                $sent++;
            }
        }

        return $sent;
    }

    /**
     * Gère les erreurs Firebase (token invalide, désinscrit, etc.)
     */
    protected function handleFirebaseError(string $token, FirebaseException $e): void
    {
        $message = $e->getMessage();

        // Token invalide ou désinscrit → désactiver
        if (
            str_contains($message, 'UNREGISTERED') ||
            str_contains($message, 'INVALID_ARGUMENT') ||
            str_contains($message, 'NOT_FOUND')
        ) {
            DeviceToken::where('token', $token)->update(['is_active' => false]);
            Log::info('[PushNotification] Token désactivé (invalide/désinscrit)', [
                'token' => substr($token, 0, 20) . '...',
            ]);
        } else {
            Log::error('[PushNotification] Erreur Firebase : ' . $message, [
                'token' => substr($token, 0, 20) . '...',
            ]);
        }
    }
}
