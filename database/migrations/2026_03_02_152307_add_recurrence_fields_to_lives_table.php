<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecurrenceFieldsToLivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lives', function (Blueprint $table) {

            if (!Schema::hasColumn('lives', 'day_of_week')) {
                $table->integer('day_of_week')->nullable();
            }

            if (!Schema::hasColumn('lives', 'start_time')) {
                $table->time('start_time')->nullable();
            }

            if (!Schema::hasColumn('lives', 'end_time')) {
                $table->time('end_time')->nullable();
            }

        });
    }


    public function down()
    {
        Schema::table('lives', function (Blueprint $table) {
            $table->dropColumn(['day_of_week', 'start_time', 'end_time']);
        });
    }

}
