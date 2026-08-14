<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParentStudentTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('parent_student')) {
            return;
        }

        Schema::create('parent_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('relationship', 40)->default('Parent');
            $table->boolean('is_primary')->default(false);
            $table->boolean('can_view_schedule')->default(true);
            $table->boolean('can_view_absences')->default(true);
            $table->boolean('can_view_assignments')->default(true);
            $table->boolean('can_view_results')->default(true);
            $table->timestamps();
            $table->unique(['parent_id', 'student_id'], 'parent_student_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('parent_student');
    }
}
