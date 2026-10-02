<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable('class_slots')
            || !Schema::hasColumn(
                'class_slots',
                'position'
            )
        ) {
            return;
        }

        /*
         * Les groupes ne sont plus limités à D1-D4/I1-I4/A1-A4.
         * Avec des heures libres à la minute, le rang peut dépasser
         * 255 : on élargit donc position de TINYINT vers INTEGER.
         *
         * SQLite (tests) n'a pas besoin de cette modification
         * physique pour les scénarios courants.
         */
        $driver =
            DB::connection()
                ->getDriverName();

        if (
            in_array(
                $driver,
                ['mysql', 'mariadb'],
                true
            )
        ) {
            DB::statement(
                'ALTER TABLE class_slots '
                . 'MODIFY position INT UNSIGNED NOT NULL DEFAULT 1'
            );

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement(
                'ALTER TABLE class_slots '
                . 'ALTER COLUMN position TYPE INTEGER'
            );
        }
    }

    public function down(): void
    {
        if (
            !Schema::hasTable('class_slots')
            || !Schema::hasColumn(
                'class_slots',
                'position'
            )
        ) {
            return;
        }

        /*
         * Ne pas rétrécir si des groupes > 255 existent.
         */
        $maxPosition =
            (int) DB::table(
                'class_slots'
            )->max(
                'position'
            );

        if ($maxPosition > 255) {
            return;
        }

        $driver =
            DB::connection()
                ->getDriverName();

        if (
            in_array(
                $driver,
                ['mysql', 'mariadb'],
                true
            )
        ) {
            DB::statement(
                'ALTER TABLE class_slots '
                . 'MODIFY position TINYINT UNSIGNED NOT NULL DEFAULT 1'
            );
        }
    }
};
