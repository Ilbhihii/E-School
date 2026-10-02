<?php

namespace App\Console\Commands;

use App\Services\AdminNotificationService;
use Illuminate\Console\Command;

class TestAdminNotification extends Command
{
    protected $signature =
        'admin-notifications:test';

    protected $description =
        'Tester les notifications administrateur par e-mail et WhatsApp';

    public function handle(
        AdminNotificationService $notifications
    ): int {
        $this->info(
            'Envoi du test de notification administrateur...'
        );

        $notifications->notify(
            'Test de notification',
            [
                'Source' =>
                    'Commande Artisan',
                'Statut' =>
                    'Configuration en cours de vérification',
            ],
            url('/admin')
        );

        $this->info(
            'Test déclenché. Vérifiez votre e-mail, WhatsApp et storage/logs/laravel.log.'
        );

        return self::SUCCESS;
    }
}