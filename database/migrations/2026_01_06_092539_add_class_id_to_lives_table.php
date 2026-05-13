<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class AddClassIdToLivesTable extends Migration
{
    public function up()
    {
        Schema::table('lives', function (Blueprint $table) {
        if (!Schema::hasColumn('lives', 'class_id')) {
            $table->foreignId('class_id')
                ->after('id')
                ->constrained('classes')
                ->onDelete('cascade');
        }
        });
    }

    public function down()
    {
        Schema::table('lives', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn('class_id');
        });
    }
}
