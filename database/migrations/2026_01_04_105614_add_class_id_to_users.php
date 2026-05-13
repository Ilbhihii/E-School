<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClassIdToUsers extends Migration
{
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'class_id')) {
            // ajouter class_id
            $table->foreignId('class_id')
                  ->nullable()
                  ->constrained('class_rooms')
                  ->onDelete('set null');
        }
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['class_id']);
        $table->dropColumn('class_id');
    });
}

}
