<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pedagogical_time_slots')) {
            return;
        }

        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $database = DB::connection()->getDatabaseName();

            $legacyUnique = DB::selectOne(
                'SELECT INDEX_NAME FROM information_schema.statistics '
                . 'WHERE table_schema = ? AND table_name = ? '
                . 'AND index_name = ? LIMIT 1',
                [
                    $database,
                    'pedagogical_time_slots',
                    'ped_time_slots_day_number_unique',
                ]
            );

            if ($legacyUnique) {
                DB::statement(
                    'ALTER TABLE `pedagogical_time_slots` '
                    . 'DROP INDEX `ped_time_slots_day_number_unique`'
                );
            }

            $normalIndex = DB::selectOne(
                'SELECT INDEX_NAME FROM information_schema.statistics '
                . 'WHERE table_schema = ? AND table_name = ? '
                . 'AND index_name = ? LIMIT 1',
                [
                    $database,
                    'pedagogical_time_slots',
                    'pts_day_number_index',
                ]
            );

            if (!$normalIndex) {
                DB::statement(
                    'ALTER TABLE `pedagogical_time_slots` '
                    . 'ADD INDEX `pts_day_number_index` '
                    . '(`day_of_week`, `slot_number`)'
                );
            }

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement(
                'DROP INDEX IF EXISTS ped_time_slots_day_number_unique'
            );
            DB::statement(
                'CREATE INDEX IF NOT EXISTS pts_day_number_index '
                . 'ON pedagogical_time_slots (day_of_week, slot_number)'
            );
            return;
        }

        if ($driver === 'sqlite') {
            DB::statement(
                'DROP INDEX IF EXISTS ped_time_slots_day_number_unique'
            );
            DB::statement(
                'CREATE INDEX IF NOT EXISTS pts_day_number_index '
                . 'ON pedagogical_time_slots (day_of_week, slot_number)'
            );
        }
    }

    public function down(): void
    {
        // Migration corrective : ne pas restaurer l'ancien index UNIQUE cassant.
    }
};
