<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScheduleIdToClassUserTable extends Migration
{
    public function up()
    {
        Schema::table('class_user', function (Blueprint $table) {
            if (!Schema::hasColumn('class_user', 'schedule_id')) {
                $table
                    ->foreignId('schedule_id')
                    ->nullable()
                    ->after('subject_id')
                    ->constrained('schedules')
                    ->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('class_user', function (Blueprint $table) {
            if (Schema::hasColumn('class_user', 'schedule_id')) {
                $table->dropForeign(['schedule_id']);
                $table->dropColumn('schedule_id');
            }
        });
    }
}
