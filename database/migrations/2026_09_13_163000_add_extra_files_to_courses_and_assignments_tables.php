<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFilesToCoursesAndAssignmentsTables extends Migration
{
    public function up()
    {
        if (
            Schema::hasTable('courses')
            && !Schema::hasColumn('courses', 'extra_files')
        ) {
            Schema::table('courses', function (Blueprint $table) {
                $table->json('extra_files')
                    ->nullable()
                    ->after('pdf');
            });
        }

        if (
            Schema::hasTable('assignments')
            && !Schema::hasColumn('assignments', 'extra_files')
        ) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->json('extra_files')
                    ->nullable()
                    ->after('file');
            });
        }
    }

    public function down()
    {
        if (
            Schema::hasTable('courses')
            && Schema::hasColumn('courses', 'extra_files')
        ) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('extra_files');
            });
        }

        if (
            Schema::hasTable('assignments')
            && Schema::hasColumn('assignments', 'extra_files')
        ) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->dropColumn('extra_files');
            });
        }
    }
}
