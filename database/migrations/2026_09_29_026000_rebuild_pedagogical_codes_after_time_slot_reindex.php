<?php

use App\Services\PedagogicalScopeCodeRebuildService;
use App\Services\PedagogicalTimeSlotService;
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
        ) {
            return;
        }

        /*
         * 1. Vérifier la numérotation chronologique.
         */
        app(
            PedagogicalTimeSlotService::class
        )->reindexAll(
            false
        );

        /*
         * 2. Recalculer les codes stockés avec les nouveaux rangs.
         */
        app(
            PedagogicalScopeCodeRebuildService::class
        )->rebuildAll();
    }

    public function down(): void
    {
        /*
         * Pas de rollback automatique :
         * les codes représentent une donnée dérivée du Jour + Heure.
         */
    }
};