<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWeekNumberToAssignmentsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('assignments', 'week_number')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table
                    ->unsignedInteger('week_number')
                    ->nullable()
                    ->after('title');

                $table->index('week_number');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('assignments', 'week_number')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->dropColumn('week_number');
            });
        }
    }
}
