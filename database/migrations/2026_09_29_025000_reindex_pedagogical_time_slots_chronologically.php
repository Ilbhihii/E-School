<?php

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

        app(
            PedagogicalTimeSlotService::class
        )->reindexAll();
    }

    public function down(): void
    {
        /*
         * Pas de rollback :
         * la migration ne fait que remettre les numéros
         * dans l'ordre chronologique demandé.
         */
    }
};