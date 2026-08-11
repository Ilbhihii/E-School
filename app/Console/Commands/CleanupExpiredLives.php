<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupExpiredLives extends Command
{
    protected $signature =
        'lives:cleanup-expired
        {--dry-run : Vérifier sans supprimer}';

    protected $description =
        'Compatibilité : les lives terminés sont conservés';

    public function handle(): int
    {
        $this->info(
            'Suppression automatique désactivée : '
            . 'les lives terminés restent dans l’historique.'
        );

        return self::SUCCESS;
    }
}
