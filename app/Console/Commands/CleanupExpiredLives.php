<?php

namespace App\Console\Commands;

use App\Models\Live;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupExpiredLives extends Command
{
    protected $signature =
        'lives:cleanup-expired
        {--dry-run : Afficher le nombre sans supprimer}';

    protected $description =
        'Masquer automatiquement les lives '
        . '24 heures après leur heure de fin';

    public function handle(): int
    {
        $query = Live::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->whereNotNull('auto_delete_at')
            ->where(
                'auto_delete_at',
                '<=',
                now()
            );

        $count = (clone $query)->count();

        if ($this->option('dry-run')) {
            $this->info(
                $count
                . ' live(s) prêt(s) '
                . 'à être supprimé(s).'
            );

            return 0;
        }

        if ($count === 0) {
            $this->info(
                'Aucun live expiré à supprimer.'
            );

            return 0;
        }

        $deleted = 0;

        $query
            ->orderBy('id')
            ->chunkById(
                100,
                function ($lives) use (
                    &$deleted
                ) {
                    DB::transaction(
                        function () use (
                            $lives,
                            &$deleted
                        ) {
                            foreach ($lives as $live) {
                                /*
                                 * Soft delete :
                                 * le live disparaît de toutes
                                 * les interfaces, mais reste
                                 * récupérable dans la base.
                                 */
                                $live->delete();
                                $deleted++;
                            }
                        }
                    );
                }
            );

        $this->info(
            $deleted
            . ' live(s) supprimé(s) '
            . 'des interfaces Student, Prof et Admin.'
        );

        return 0;
    }
}
