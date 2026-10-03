<?php

namespace App\Console\Commands;

use App\Models\DeviceToken;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class NotificationDiagnostics extends Command
{
    protected $signature = 'notifications:diagnose';

    protected $description = 'Vérifie la configuration Firebase et les appareils enregistrés';

    public function handle(PushNotificationService $push): int
    {
        $checks = [
            ['Push activé', (bool) config('push.enabled', true)],
            ['Firebase serveur', $push->isConfigured()],
            ['Web API key', trim((string) config('push.web.firebase.api_key')) !== ''],
            ['Web project ID', trim((string) config('push.web.firebase.project_id')) !== ''],
            ['Web sender ID', trim((string) config('push.web.firebase.messaging_sender_id')) !== ''],
            ['Web app ID', trim((string) config('push.web.firebase.app_id')) !== ''],
            ['Web VAPID key', trim((string) config('push.web.vapid_key')) !== ''],
        ];

        foreach ($checks as [$label, $ok]) {
            $this->line(($ok ? '✓ ' : '✗ ') . $label);
        }

        if (Schema::hasTable('device_tokens')) {
            $this->newLine();
            $this->line('Appareils actifs :');

            foreach (['web', 'android', 'ios'] as $platform) {
                $count = DeviceToken::query()
                    ->active()
                    ->where('platform', $platform)
                    ->count();

                $this->line("- {$platform}: {$count}");
            }
        }

        $ok = collect($checks)->every(fn ($check) => (bool) $check[1]);

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
