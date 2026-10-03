<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProductionPreflight extends Command
{
    protected $signature = 'production:preflight';

    protected $description =
        'Vérifie les points critiques avant un déploiement en production.';

    public function handle(): int
    {
        $failures = [];
        $warnings = [];

        if (!app()->environment('production')) {
            $failures[] =
                'APP_ENV doit être production.';
        }

        if ((bool) config('app.debug')) {
            $failures[] =
                'APP_DEBUG doit être false en production.';
        }

        $appUrl = (string) config('app.url');

        if (!str_starts_with($appUrl, 'https://')) {
            $failures[] =
                'APP_URL doit utiliser HTTPS.';
        }

        $origins = (array) config('cors.allowed_origins', []);

        if (in_array('*', $origins, true)) {
            $failures[] =
                'CORS_ALLOWED_ORIGINS ne doit pas être * en production.';
        }

        $publicBackups = glob(
            public_path('storage_backup_*'),
            GLOB_ONLYDIR
        ) ?: [];

        if (!empty($publicBackups)) {
            $failures[] =
                'Un dossier storage_backup_* est encore présent sous public/.';
        }

        if (config('mail.default') === 'log') {
            $warnings[] =
                'MAIL_MAILER utilise encore log : les e-mails réels ne partiront pas.';
        }

        $freeBytes = @disk_free_space(storage_path());

        if (
            is_numeric($freeBytes)
            && (float) $freeBytes < 5 * 1024 * 1024 * 1024
        ) {
            $warnings[] =
                'Moins de 5 Go libres sur le disque de storage/. '
                . 'Attention aux fichiers pédagogiques jusqu’à 2 Go.';
        }

        foreach (
            [
                'upload_max_filesize' => 2 * 1024 * 1024 * 1024,
                'post_max_size' => 2 * 1024 * 1024 * 1024,
            ]
            as $setting => $minimum
        ) {
            $value = (string) ini_get($setting);
            $bytes = $this->iniSizeToBytes($value);

            if ($bytes > 0 && $bytes < $minimum) {
                $warnings[] =
                    $setting
                    . ' vaut '
                    . $value
                    . ' pour PHP CLI. Vérifiez aussi PHP-FPM pour la limite 2 Go.';
            }
        }

        $this->info('Smart School Academy — contrôle pré-déploiement');
        $this->newLine();

        if (empty($failures)) {
            $this->info('✓ Aucun blocage critique détecté.');
        } else {
            foreach ($failures as $failure) {
                $this->error('✗ ' . $failure);
            }
        }

        foreach ($warnings as $warning) {
            $this->warn('! ' . $warning);
        }

        $this->newLine();

        if (!empty($failures)) {
            $this->error(
                'Déploiement déconseillé tant que les erreurs ci-dessus ne sont pas corrigées.'
            );

            return self::FAILURE;
        }

        $this->info('Préflight OK.');

        return self::SUCCESS;
    }

    private function iniSizeToBytes(string $value): int
    {
        $value = trim($value);

        if ($value === '') {
            return 0;
        }

        $last = strtolower(substr($value, -1));
        $number = (float) $value;

        return match ($last) {
            'g' => (int) ($number * 1024 * 1024 * 1024),
            'm' => (int) ($number * 1024 * 1024),
            'k' => (int) ($number * 1024),
            default => (int) $number,
        };
    }
}
