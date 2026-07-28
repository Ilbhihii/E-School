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
        // Sécurité : ne pas détruire une table créée par une autre migration
        // La table originale est créée par 2026_01_05_000000
        // Cette migration est un correctif de compatibilité uniquement
    }
};

