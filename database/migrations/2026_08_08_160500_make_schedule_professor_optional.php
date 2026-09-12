<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (
            Schema::hasTable('schedules')
            && Schema::hasColumn('schedules', 'prof_id')
        ) {
            if (DB::getDriverName() === 'sqlite') {
                Schema::table('schedules', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->unsignedBigInteger('prof_id')->nullable()->change();
                });
                return;
            }
            /*
             * Le professeur n'est plus obligatoire lors de la création
             * du créneau. Il pourra être associé séparément.
             *
             * Requête MySQL directe pour éviter une dépendance Doctrine DBAL.
             */
            DB::statement(
                'ALTER TABLE schedules MODIFY prof_id BIGINT UNSIGNED NULL'
            );
        }
    }

    public function down()
    {
        if (
            Schema::hasTable('schedules')
            && Schema::hasColumn('schedules', 'prof_id')
        ) {
            /*
             * Avant de revenir en NOT NULL, supprimer les NULL
             * en utilisant un professeur existant si possible.
             */
            $professorId = DB::table('users')
                ->where('role', 'prof')
                ->value('id');

            if ($professorId) {
                DB::table('schedules')
                    ->whereNull('prof_id')
                    ->update([
                        'prof_id' => $professorId,
                    ]);

                if (DB::getDriverName() === 'sqlite') {
                    Schema::table('schedules', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->unsignedBigInteger('prof_id')->nullable(false)->change();
                    });
                } else {
                    DB::statement(
                        'ALTER TABLE schedules MODIFY prof_id BIGINT UNSIGNED NOT NULL'
                    );
                }
            }
        }
    }
};
