<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocal_test_prompts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnDelete();

            $table->foreignId('level_id')
                ->constrained('levels')
                ->cascadeOnDelete();

            $table->foreignId('class_id')
                ->constrained('class_rooms')
                ->cascadeOnDelete();

            $table->string('title');

            $table->text('instructions')->nullable();

            $table->longText('reading_text');

            $table->unsignedInteger('maximum_duration')
                ->default(120);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->unique(
                ['subject_id', 'level_id', 'class_id'],
                'unique_vocal_test_prompt'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocal_test_prompts');
    }
};
