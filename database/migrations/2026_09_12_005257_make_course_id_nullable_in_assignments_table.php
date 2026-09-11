<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeCourseIdNullableInAssignmentsTable extends Migration
{
    public function up()
    {
        DB::statement(
            'ALTER TABLE assignments MODIFY course_id BIGINT UNSIGNED NULL'
        );
    }

    public function down()
    {
        DB::statement(
            'ALTER TABLE assignments MODIFY course_id BIGINT UNSIGNED NOT NULL'
        );
    }
}