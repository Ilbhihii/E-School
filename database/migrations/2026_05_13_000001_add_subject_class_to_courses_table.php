<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete()->after('description');
            $table->foreignId('class_id')->constrained('class_rooms')->cascadeOnDelete()->after('subject_id');
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['class_id']);
            $table->dropColumn(['subject_id', 'class_id']);
        });
    }
};

