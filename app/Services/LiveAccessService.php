<?php

namespace App\Services;

use App\Models\Live;
use App\Models\LiveAccessLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LiveAccessService
{
    public function evaluate(
        ?User $user,
        Live $live
    ): array {
        if (!$user) {
            return $this->denied(
                'authentication_required',
                'Connectez-vous pour accéder à cette session.'
            );
        }

        if (!$live->stream_url) {
            return $this->denied(
                'missing_link',
                'Le lien de cette session n’est pas encore disponible.'
            );
        }

        if ($live->is_ended) {
            return $this->denied(
                'session_ended',
                'Cette session est terminée.'
            );
        }

        if ($user->isAdmin() || $user->isProf()) {
            return $this->allowed();
        }

        if (!$user->isStudent()) {
            return $this->denied(
                'role_not_allowed',
                'Votre compte ne permet pas de rejoindre cette session.'
            );
        }

        if (!(bool) $user->is_active) {
            return $this->denied(
                'account_inactive',
                'Votre compte doit être activé par l’administration.'
            );
        }

        if (!$this->hasPaidAccess($user)) {
            return $this->denied(
                'payment_required',
                'Un abonnement actif est nécessaire pour rejoindre les lives.'
            );
        }

        if (!$live->class_id) {
            return $this->denied(
                'class_missing',
                'Cette session n’est associée à aucune classe.'
            );
        }

        if (!$this->belongsToLiveClass($user, $live)) {
            return $this->denied(
                'class_not_allowed',
                'Cette session n’est pas destinée à votre classe.'
            );
        }

        if ($live->schedule_status === 'unscheduled') {
            return $this->denied(
                'session_unscheduled',
                'La date de cette session n’est pas encore confirmée.'
            );
        }

        $start = $live->start_date_time;

        if ($start) {
            $availableAt = $start->copy()->subMinutes(
                max(
                    0,
                    (int) config(
                        'live.join_early_minutes',
                        15
                    )
                )
            );

            if (now()->lt($availableAt)) {
                return $this->denied(
                    'too_early',
                    'L’accès sera disponible le '
                    . $availableAt->format('d/m/Y à H:i')
                    . '.',
                    $availableAt
                );
            }
        }

        return $this->allowed();
    }

    public function record(
        Request $request,
        Live $live,
        array $decision
    ): void {
        try {
            LiveAccessLog::create([
                'live_id' => $live->id,
                'user_id' => optional(
                    $request->user()
                )->id,
                'status' => $decision['allowed']
                    ? 'allowed'
                    : 'denied',
                'reason' => $decision['code'] ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => mb_substr(
                    (string) $request->userAgent(),
                    0,
                    1000
                ),
            ]);
        } catch (\Throwable $exception) {
            /*
             * Le journal ne doit jamais empêcher l'accès au live.
             */
            report($exception);
        }
    }

    public function hasPaidAccess(User $user): bool
    {
        return (bool) $user->is_paid
            || (bool) (
                $user->getAttribute('is_subscribed')
                ?? false
            );
    }

    private function belongsToLiveClass(
        User $user,
        Live $live
    ): bool {
        if (
            (int) $user->class_id
            === (int) $live->class_id
        ) {
            return true;
        }

        /*
         * Compatibilité avec les assignations individuelles déjà
         * utilisées par StudentController.
         */
        if (!Schema::hasTable('class_user')) {
            return false;
        }

        return DB::table('class_user')
            ->where('user_id', $user->id)
            ->where('class_id', $live->class_id)
            ->exists();
    }

    private function allowed(): array
    {
        return [
            'allowed' => true,
            'code' => 'allowed',
            'message' => 'Accès autorisé.',
            'available_at' => null,
        ];
    }

    private function denied(
        string $code,
        string $message,
        ?Carbon $availableAt = null
    ): array {
        return [
            'allowed' => false,
            'code' => $code,
            'message' => $message,
            'available_at' => $availableAt,
        ];
    }
}
