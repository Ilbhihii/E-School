<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table already correctly created with unique constraint and timestamps
        // by 2026_01_05_000000_create_class_room_subject_table.php
        if (!Schema::hasTable('class_room_subject')) {
            Schema::create('class_room_subject', function (Blueprint $table) {
                $table->id();
                $table->foreignId('class_room_id')->constrained('class_rooms')->onDelete('cascade');
                $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
                $table->timestamps();
                $table->unique(['class_room_id', 'subject_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('class_room_subject');
    }
};
