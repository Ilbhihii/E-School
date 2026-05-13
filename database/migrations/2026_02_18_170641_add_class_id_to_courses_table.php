<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClassIdToCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            // add the foreign key only if it doesn't already exist
            if (! Schema::hasColumn('courses', 'class_id')) {
                $table->unsignedBigInteger('class_id')->nullable(); // nullable si tu veux
                $table->foreign('class_id')
                      ->references('id')
                      ->on('classes')
                      ->onDelete('set null');
            }
        });

    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'class_id')) {
                $table->dropForeign(['class_id']);
                $table->dropColumn('class_id');
            }
        });
    }

}
