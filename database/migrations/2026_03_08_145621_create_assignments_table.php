<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('assignments')) {
            Schema::create('assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // étudiant
                $table->string('title');
                $table->string('file'); // fichier devoir
                $table->foreignId('class_room_id')->nullable()->constrained('class_rooms');
                $table->foreignId('subject_id')->nullable()->constrained('subjects');
                $table->date('due_date')->nullable();
                $table->text('description')->nullable();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->integer('grade')->nullable();
                $table->text('comment')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};

