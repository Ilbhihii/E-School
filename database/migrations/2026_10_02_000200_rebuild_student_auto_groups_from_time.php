<?php

use App\Services\PedagogicalScopeCodeRebuildService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable(
                'pedagogical_time_slots'
            )
            || !Schema::hasTable(
                'class_slots'
            )
        ) {
            return;
        }

        /*
         * Aligne également les anciennes assignations :
         *
         * heure -> rang chronologique -> groupe automatique.
         *
         * Exemple Débutant :
         * 08:00 -> D1
         * 08:30 -> D2
         * 08:45 -> D3
         * 09:00 -> D4
         *
         * Les codes pédagogiques liés sont reconstruits dans
         * le nouveau format D1ARLED1 / D1ARLED2 / ...
         */
        app(
            PedagogicalScopeCodeRebuildService::class
        )->rebuildAll();
    }

    public function down(): void
    {
        /*
         * Migration métier non destructive :
         * on ne restaure pas les anciens groupes manuels.
         */
    }
};
