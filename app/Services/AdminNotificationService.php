<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AdminNotificationService
{
    public function notify(
        string $title,
        array $details = [],
        ?string $url = null
    ): void {
        if (
            !config(
                'admin_notifications.enabled',
                true
            )
        ) {
            return;
        }

        $message =
            $this->buildMessage(
                $title,
                $details,
                $url
            );

        $this->sendEmail(
            $title,
            $message
        );

        $this->sendWhatsApp(
            $message
        );

        $this->sendInAppAndPush(
            $title,
            $details,
            $url
        );
    }


    private function sendInAppAndPush(
        string $title,
        array $details,
        ?string $url
    ): void {
        try {
            $summary = collect($details)
                ->filter(
                    fn ($value) =>
                        $value !== null
                        && trim((string) $value) !== ''
                )
                ->take(3)
                ->map(
                    fn ($value, $label) =>
                        trim((string) $label)
                        . ' : '
                        . trim((string) $value)
                )
                ->implode(' · ');

            app(NotificationCenterService::class)
                ->sendToAdmins(
                    $title,
                    $summary !== ''
                        ? $summary
                        : 'Nouvelle activité à consulter.',
                    'general',
                    $url,
                    'bi bi-bell-fill',
                    ['source' => 'admin_notification_service'],
                    true,
                    'high'
                );
        } catch (Throwable $exception) {
            Log::warning(
                'Notification push administrateur ignorée.',
                [
                    'title' => $title,
                    'exception' => $exception->getMessage(),
                ]
            );
        }
    }

    private function buildMessage(
        string $title,
        array $details,
        ?string $url
    ): string {
        $lines = [
            'Smart School Academy',
            '',
            $title,
            '',
        ];

        foreach ($details as $label => $value) {
            if (
                $value === null
                || $value === ''
            ) {
                continue;
            }

            $lines[] =
                trim((string) $label)
                . ' : '
                . trim((string) $value);
        }

        if ($url) {
            $lines[] = '';
            $lines[] = 'Voir : ' . $url;
        }

        $lines[] = '';
        $lines[] =
            'Date : '
            . now()
                ->timezone(
                    config(
                        'app.timezone',
                        'UTC'
                    )
                )
                ->format(
                    'd/m/Y H:i'
                );

        return implode(
            "\n",
            $lines
        );
    }

    private function sendEmail(
        string $title,
        string $message
    ): void {
        if (
            !config(
                'admin_notifications.email.enabled',
                true
            )
        ) {
            return;
        }

        $recipient = trim(
            (string) config(
                'admin_notifications.email.to',
                ''
            )
        );

        if (
            $recipient === ''
            || !filter_var(
                $recipient,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            return;
        }

        try {
            Mail::raw(
                $message,
                function ($mail) use (
                    $recipient,
                    $title
                ) {
                    $mail
                        ->to($recipient)
                        ->subject(
                            '[Smart School] '
                            . $title
                        );
                }
            );
        } catch (Throwable $exception) {
            Log::error(
                'Échec notification admin par e-mail.',
                [
                    'title' =>
                        $title,
                    'recipient' =>
                        $recipient,
                    'exception' =>
                        $exception->getMessage(),
                ]
            );
        }
    }

    private function sendWhatsApp(
        string $message
    ): void {
        if (
            !config(
                'admin_notifications.whatsapp.enabled',
                false
            )
        ) {
            return;
        }

        $recipient =
            preg_replace(
                '/\D+/',
                '',
                (string) config(
                    'admin_notifications.whatsapp.to',
                    ''
                )
            );

        $version = trim(
            (string) config(
                'admin_notifications.whatsapp.graph_version',
                ''
            )
        );

        $phoneNumberId = trim(
            (string) config(
                'admin_notifications.whatsapp.phone_number_id',
                ''
            )
        );

        $token = trim(
            (string) config(
                'admin_notifications.whatsapp.access_token',
                ''
            )
        );

        if (
            $recipient === ''
            || $version === ''
            || $phoneNumberId === ''
            || $token === ''
        ) {
            Log::warning(
                'Notification WhatsApp ignorée : configuration incomplète.'
            );

            return;
        }

        $endpoint =
            'https://graph.facebook.com/'
            . rawurlencode($version)
            . '/'
            . rawurlencode($phoneNumberId)
            . '/messages';

        $mode = strtolower(
            trim(
                (string) config(
                    'admin_notifications.whatsapp.mode',
                    'template'
                )
            )
        );

        if ($mode === 'text') {
            $payload = [
                'messaging_product' =>
                    'whatsapp',
                'to' =>
                    $recipient,
                'type' =>
                    'text',
                'text' => [
                    'preview_url' =>
                        false,
                    'body' =>
                        $message,
                ],
            ];
        } else {
            $templateName = trim(
                (string) config(
                    'admin_notifications.whatsapp.template_name',
                    'admin_notification'
                )
            );

            $language = trim(
                (string) config(
                    'admin_notifications.whatsapp.template_language',
                    'fr'
                )
            );

            $payload = [
                'messaging_product' =>
                    'whatsapp',
                'to' =>
                    $recipient,
                'type' =>
                    'template',
                'template' => [
                    'name' =>
                        $templateName,
                    'language' => [
                        'code' =>
                            $language,
                    ],
                    'components' => [
                        [
                            'type' =>
                                'body',
                            'parameters' => [
                                [
                                    'type' =>
                                        'text',
                                    'text' =>
                                        mb_substr(
                                            $message,
                                            0,
                                            3500
                                        ),
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        try {
            $response =
                Http::withToken($token)
                    ->acceptJson()
                    ->timeout(10)
                    ->post(
                        $endpoint,
                        $payload
                    );

            if (!$response->successful()) {
                Log::error(
                    'Échec notification admin WhatsApp.',
                    [
                        'status' =>
                            $response->status(),
                        'response' =>
                            mb_substr(
                                $response->body(),
                                0,
                                2000
                            ),
                    ]
                );
            }
        } catch (Throwable $exception) {
            Log::error(
                'Exception notification admin WhatsApp.',
                [
                    'exception' =>
                        $exception->getMessage(),
                ]
            );
        }
    }
}