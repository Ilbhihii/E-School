<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScheduleToLivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lives', function (Blueprint $table) {
            if (! Schema::hasColumn('lives', 'live_date')) {
                $table->date('live_date');
            }
            if (! Schema::hasColumn('lives', 'start_time')) {
                $table->time('start_time');
            }
            if (! Schema::hasColumn('lives', 'end_time')) {
                $table->time('end_time');
            }
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lives', function (Blueprint $table) {
            if (Schema::hasColumn('lives', 'live_date')) {
                $table->dropColumn('live_date');
            }
            if (Schema::hasColumn('lives', 'start_time')) {
                $table->dropColumn('start_time');
            }
            if (Schema::hasColumn('lives', 'end_time')) {
                $table->dropColumn('end_time');
            }
        });
    }
}
