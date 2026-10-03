<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class PushNotificationService
{
    protected ?Messaging $messaging = null;

    public function __construct()
    {
        try {
            $this->messaging = app(Messaging::class);
        } catch (\Throwable $e) {
            Log::warning(
                'Firebase non configuré : ' . $e->getMessage()
            );

            $this->messaging = null;
        }
    }

    public function isConfigured(): bool
    {
        return (bool) config('push.enabled', true)
            && $this->messaging !== null;
    }

    public function sendToToken(
        string $token,
        string $title,
        string $body,
        array $data = []
    ): bool {
        if (!$this->isConfigured()) {
            Log::info(
                '[PushNotification] Firebase non configuré. Notification ignorée.',
                ['title' => $title]
            );

            return false;
        }

        $data = $this->stringifyData($data);
        $data['title'] = $title;
        $data['body'] = $body;

        try {
            $message = CloudMessage::new()
                ->withNotification(
                    Notification::create(
                        $title,
                        $body
                    )
                )
                ->withData($data)
                ->withChangedTarget(
                    'token',
                    $token
                );

            $this->messaging->send($message);

            return true;
        } catch (\Throwable $e) {
            $this->handleFirebaseError(
                $token,
                $e
            );

            return false;
        }
    }

    public function sendToUser(
        User $user,
        string $title,
        string $body,
        array $data = []
    ): int {
        $devices = $user
            ->deviceTokens()
            ->active()
            ->get();

        $sent = 0;

        foreach ($devices as $device) {
            if (
                $this->sendToToken(
                    (string) $device->token,
                    $title,
                    $body,
                    array_merge(
                        $data,
                        [
                            'platform' =>
                                (string) ($device->platform ?? ''),
                        ]
                    )
                )
            ) {
                $sent++;
            }
        }

        return $sent;
    }

    public function sendToUsers(
        iterable $users,
        string $title,
        string $body,
        array $data = []
    ): int {
        $sent = 0;

        foreach ($users as $user) {
            if ($user instanceof User) {
                $sent += $this->sendToUser(
                    $user,
                    $title,
                    $body,
                    $data
                );
            }
        }

        return $sent;
    }

    public function sendToPlatform(
        string $platform,
        string $title,
        string $body,
        array $data = []
    ): int {
        $tokens = DeviceToken::query()
            ->active()
            ->platform($platform)
            ->get();

        $sent = 0;

        foreach ($tokens as $device) {
            if (
                $this->sendToToken(
                    (string) $device->token,
                    $title,
                    $body,
                    array_merge(
                        $data,
                        ['platform' => $platform]
                    )
                )
            ) {
                $sent++;
            }
        }

        return $sent;
    }

    protected function stringifyData(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $result[(string) $key] = (string) ($value ?? '');
            }
        }

        return $result;
    }

    protected function handleFirebaseError(
        string $token,
        \Throwable $exception
    ): void {
        $message = $exception->getMessage();
        $upper = strtoupper($message);

        $invalid =
            str_contains($upper, 'UNREGISTERED')
            || str_contains($upper, 'INVALID_ARGUMENT')
            || str_contains($upper, 'NOT_FOUND')
            || str_contains($upper, 'REGISTRATION TOKEN IS NOT A VALID')
            || str_contains($upper, 'REQUESTED ENTITY WAS NOT FOUND');

        if ($invalid) {
            DeviceToken::query()
                ->where(
                    'token_hash',
                    DeviceToken::hashToken($token)
                )
                ->update(['is_active' => false]);

            Log::info(
                '[PushNotification] Token FCM désactivé.',
                [
                    'token_hash' =>
                        DeviceToken::hashToken($token),
                ]
            );

            return;
        }

        Log::error(
            '[PushNotification] Erreur Firebase.',
            [
                'exception' => $message,
                'token_hash' =>
                    DeviceToken::hashToken($token),
            ]
        );
    }
}
