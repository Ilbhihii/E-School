<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('class_room_subject')) {
            return;
        }
        Schema::create('class_room_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->cascadeOnDelete();
             $table->foreignId('class_room_id')
                  ->constrained('class_rooms')
                  ->cascadeOnDelete();
            $table->unique(['subject_id','class_room_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_room_subject');
    }
};

