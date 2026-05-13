<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixTestsRenameCourseToSubjectSafe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Disabled: already renamed by previous migration
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->renameColumn('subject_id', 'course_id');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }
}
