<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationCenterService;
use Illuminate\Console\Command;

class SendTestNotification extends Command
{
    protected $signature = 'notifications:test
                            {--user= : ID exact du compte à notifier}
                            {--role= : admin, prof, student ou parent}';

    protected $description = 'Envoie une notification de test au centre de notifications';

    public function handle(NotificationCenterService $notifications): int
    {
        if ($this->option('user')) {
            $user = User::find($this->option('user'));

            if (!$user) {
                $this->error('Utilisateur introuvable.');
                return self::FAILURE;
            }

            $notifications->send(
                $user,
                '🔔 Notification de test',
                'Le centre de notifications Smart School Academy fonctionne correctement.',
                'general',
                $user->dashboardRoute(),
                'bi bi-bell-fill',
                ['test' => '1']
            );

            $this->info("Notification envoyée à {$user->name} ({$user->role}).");
            return self::SUCCESS;
        }

        if ($this->option('role')) {
            $role = (string) $this->option('role');

            if (!in_array($role, ['admin', 'prof', 'student', 'parent'], true)) {
                $this->error('Rôle invalide. Utilise admin, prof, student ou parent.');
                return self::FAILURE;
            }

            $count = $notifications->sendToRole(
                $role,
                '🔔 Notification de test',
                'Le centre de notifications Smart School Academy fonctionne correctement.',
                'general',
                null,
                'bi bi-bell-fill',
                ['test' => '1'],
                false
            );

            $this->info("{$count} notification(s) envoyée(s) au rôle {$role}.");
            return self::SUCCESS;
        }

        $sent = 0;

        foreach (['admin', 'prof', 'student', 'parent'] as $role) {
            $user = User::query()
                ->where('role', $role)
                ->where('is_active', true)
                ->first();

            if (!$user) {
                $this->warn("Aucun compte actif pour {$role}.");
                continue;
            }

            $notifications->send(
                $user,
                '🔔 Test — ' . ucfirst($role),
                'Cette notification confirme que la cloche fonctionne pour votre espace.',
                'general',
                $user->dashboardRoute(),
                'bi bi-bell-fill',
                ['test' => '1'],
                false
            );

            $this->line("✓ {$role}: {$user->name}");
            $sent++;
        }

        $this->info("Test terminé : {$sent} rôle(s) notifié(s).");
        return self::SUCCESS;
    }
}
