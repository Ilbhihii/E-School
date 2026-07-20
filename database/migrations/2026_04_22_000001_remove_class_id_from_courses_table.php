<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // SQLite ne sait pas supprimer cette clé étrangère. La migration
        // suivante réajoute immédiatement class_id : on conserve donc la
        // colonne pendant les tests SQLite, ce qui produit le même schéma final.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'class_id')) {
                $table->dropForeign(['class_id']);
                $table->dropColumn('class_id');
            }
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'class_id')) {
                $table->foreignId('class_id')
                      ->nullable()
                      ->constrained('class_rooms')
                      ->cascadeOnDelete();
            }
        });
    }
};
