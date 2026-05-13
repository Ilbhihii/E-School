<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('absences')) {
            Schema::create('absences', function (Blueprint $table) {

                $table->id();

                $table->foreignId('user_id')->constrained()->cascadeOnDelete();

                $table->date('date');

                $table->boolean('present')->default(1);

                $table->timestamps();

            });
        }
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absences');
    }
}
