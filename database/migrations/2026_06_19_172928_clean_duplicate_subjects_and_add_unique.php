<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanDuplicateSubjectsAndAddUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Supprimer les matières en double (garder le plus petit ID pour chaque nom)
        $duplicates = DB::select('SELECT name, MIN(id) as keep_id FROM subjects GROUP BY name HAVING COUNT(*) > 1');

        foreach ($duplicates as $dup) {
            DB::delete('DELETE FROM subjects WHERE name = ? AND id != ?', [$dup->name, $dup->keep_id]);
        }

        // 2. Ajouter une contrainte UNIQUE sur le nom
        Schema::table('subjects', function (Blueprint $table) {
            $table->unique('name', 'subjects_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Supprimer la contrainte UNIQUE
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropUnique('subjects_name_unique');
        });

        // Note : les lignes supprimées ne peuvent pas être restaurées automatiquement.
    }
}
