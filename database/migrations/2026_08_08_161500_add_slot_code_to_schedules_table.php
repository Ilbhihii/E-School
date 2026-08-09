<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlotCodeToSchedulesTable extends Migration
{
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('schedules', 'slot_code')) {
                $table
                    ->string('slot_code', 20)
                    ->nullable()
                    ->after('class_id')
                    ->index();
            }
        });
    }

    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            if (Schema::hasColumn('schedules', 'slot_code')) {
                $table->dropIndex(['slot_code']);
                $table->dropColumn('slot_code');
            }
        });
    }
}
