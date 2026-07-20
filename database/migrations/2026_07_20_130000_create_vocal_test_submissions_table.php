<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocal_test_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('level_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('class_rooms')->cascadeOnDelete();
            $table->text('recitation_text');
            $table->string('audio_path');
            $table->string('audio_mime_type')->nullable();
            $table->timestamp('consumed_at')->nullable();
            $table->timestamps();
        });

        Schema::table('test_appointments', function (Blueprint $table) {
            $table->foreignId('vocal_test_submission_id')
                ->nullable()
                ->unique()
                ->after('type')
                ->constrained('vocal_test_submissions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('test_appointments', function (Blueprint $table) {
            $table->dropForeign(['vocal_test_submission_id']);
            $table->dropUnique(['vocal_test_submission_id']);
            $table->dropColumn('vocal_test_submission_id');
        });

        Schema::dropIfExists('vocal_test_submissions');
    }
};
