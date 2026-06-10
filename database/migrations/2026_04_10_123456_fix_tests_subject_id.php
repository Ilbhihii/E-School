<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add subject_id column
        if (!Schema::hasColumn('tests', 'subject_id')) {
            Schema::table('tests', function (Blueprint $table) {
                $table->unsignedBigInteger('subject_id')->nullable()->after('course_id');
            });
        }

        // Backfill subject_id from courses.subject_id
        DB::statement('
            UPDATE tests 
            SET subject_id = (
                SELECT c.subject_id 
                FROM courses c 
                WHERE c.id = tests.course_id
            )
            WHERE tests.course_id IS NOT NULL
                AND tests.subject_id IS NULL
        ');

        // Drop course_id FK if exists
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
        });

        // Add proper FK on subject_id
        Schema::table('tests', function (Blueprint $table) {
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->after('title');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn('subject_id');
        });
    }
};

