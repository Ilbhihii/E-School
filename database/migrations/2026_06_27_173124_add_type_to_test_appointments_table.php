<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToTestAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::table('test_appointments', function (Blueprint $table) {
            $table->string('type', 50)->default('test')->after('email');
        });
    }

    public function down()
    {
        Schema::table('test_appointments', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
