<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable('lives')
            || !Schema::hasTable('class_rooms')
            || !Schema::hasColumn('lives', 'class_id')
        ) {
            return;
        }

        $this->dropClassIdForeignKey();

        /*
         * La colonne doit accepter NULL afin de conserver les anciens lives
         * dont l'identifiant pointait vers l'ancienne table `classes`.
         */
        DB::statement(
            'ALTER TABLE `lives` '
            . 'MODIFY `class_id` BIGINT UNSIGNED NULL'
        );

        /*
         * Ne pas rattacher automatiquement un ancien live à une mauvaise
         * classe. Les identifiants qui n'existent pas dans class_rooms
         * deviennent NULL et le live reste conservé.
         */
        DB::statement(
            'UPDATE `lives` AS `live_rows` '
            . 'LEFT JOIN `class_rooms` AS `room_rows` '
            . 'ON `room_rows`.`id` = `live_rows`.`class_id` '
            . 'SET `live_rows`.`class_id` = NULL '
            . 'WHERE `live_rows`.`class_id` IS NOT NULL '
            . 'AND `room_rows`.`id` IS NULL'
        );

        Schema::table('lives', function (Blueprint $table) {
            $table->foreign('class_id')
                ->references('id')
                ->on('class_rooms')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (
            !Schema::hasTable('lives')
            || !Schema::hasTable('classes')
            || !Schema::hasColumn('lives', 'class_id')
        ) {
            return;
        }

        $this->dropClassIdForeignKey();

        /*
         * En cas de rollback, seuls les identifiants existant encore dans
         * l'ancienne table classes peuvent être conservés.
         */
        DB::statement(
            'UPDATE `lives` AS `live_rows` '
            . 'LEFT JOIN `classes` AS `old_classes` '
            . 'ON `old_classes`.`id` = `live_rows`.`class_id` '
            . 'SET `live_rows`.`class_id` = NULL '
            . 'WHERE `live_rows`.`class_id` IS NOT NULL '
            . 'AND `old_classes`.`id` IS NULL'
        );

        Schema::table('lives', function (Blueprint $table) {
            $table->foreign('class_id')
                ->references('id')
                ->on('classes')
                ->cascadeOnDelete();
        });
    }

    private function dropClassIdForeignKey(): void
    {
        $databaseName = DB::getDatabaseName();

        $foreignKey = DB::table(
            'information_schema.KEY_COLUMN_USAGE'
        )
            ->where('TABLE_SCHEMA', $databaseName)
            ->where('TABLE_NAME', 'lives')
            ->where('COLUMN_NAME', 'class_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->first();

        if (!$foreignKey) {
            return;
        }

        $constraintName = str_replace(
            '`',
            '``',
            (string) $foreignKey->CONSTRAINT_NAME
        );

        DB::statement(
            "ALTER TABLE `lives` "
            . "DROP FOREIGN KEY `{$constraintName}`"
        );
    }
};