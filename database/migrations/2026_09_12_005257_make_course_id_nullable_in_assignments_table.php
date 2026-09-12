<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MakeCourseIdNullableInAssignmentsTable extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('assignments', function (Blueprint $table) {
                $table->unsignedBigInteger('course_id')->nullable()->change();
            });
            return;
        }
        DB::statement('ALTER TABLE assignments MODIFY course_id BIGINT UNSIGNED NULL');
    }

    public function down()
    {
        if (DB::getDriverName() === 'sqlite') {
            return; // rollback destructif évité dans le schéma de test
        }
        DB::statement('ALTER TABLE assignments MODIFY course_id BIGINT UNSIGNED NOT NULL');
    }
}
