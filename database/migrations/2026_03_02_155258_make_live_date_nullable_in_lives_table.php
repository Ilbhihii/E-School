<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeLiveDateNullableInLivesTable extends Migration
{
    public function up()
    {
        Schema::table('lives', function (Blueprint $table) {
            $table->dateTime('live_date')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('lives', function (Blueprint $table) {
            $table->dateTime('live_date')->nullable(false)->change();
        });
    }
}
