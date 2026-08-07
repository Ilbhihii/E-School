<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocal_test_submission_professor', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vocal_test_submission_id')
                ->constrained('vocal_test_submissions')
                ->cascadeOnDelete();

            $table->foreignId('prof_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                ['vocal_test_submission_id', 'prof_id'],
                'vocal_submission_prof_unique'
            );

            $table->index(
                ['prof_id', 'vocal_test_submission_id'],
                'vocal_submission_prof_lookup'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocal_test_submission_professor');
    }
};
