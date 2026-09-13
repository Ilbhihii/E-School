<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPreferredAvailabilityToProfAssignmentsTable extends Migration
{
    public function up()
    {
        Schema::table('prof_assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('preferred_availability_id')
                ->nullable()
                ->after('class_slot_id');

            $table->foreign('preferred_availability_id')
                ->references('id')
                ->on('professor_availabilities')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('prof_assignments', function (Blueprint $table) {
            $table->dropForeign([
                'preferred_availability_id',
            ]);

            $table->dropColumn(
                'preferred_availability_id'
            );
        });
    }
}
