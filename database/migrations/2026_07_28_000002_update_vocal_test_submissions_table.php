<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vocal_test_submissions', function (Blueprint $table) {
            // Add vocal_test_prompt_id (nullable for backward compat with existing Coran submissions)
            $table->foreignId('vocal_test_prompt_id')
                ->nullable()
                ->after('user_id')
                ->constrained('vocal_test_prompts')
                ->nullOnDelete();

            // Rename recitation_text -> reading_text
            // (Laravel doesn't have renameColumn for all drivers, so we add new and drop old)
            $table->longText('reading_text')->nullable()->after('vocal_test_prompt_id');

            // New fields for the enhanced submission system
            $table->string('audio_original_name')->nullable()->after('audio_path');
            $table->unsignedBigInteger('audio_size')->nullable()->after('audio_mime_type');
            $table->unsignedInteger('duration_seconds')->nullable()->after('audio_size');

            $table->enum('status', [
                'submitted',
                'under_review',
                'reviewed',
                'accepted',
                'needs_improvement',
            ])->default('submitted')->after('duration_seconds');

            $table->text('teacher_comment')->nullable()->after('status');
            $table->unsignedTinyInteger('score')->nullable()->after('teacher_comment');
            $table->timestamp('submitted_at')->nullable()->after('score');
            $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
        });

        // Migrate existing recitation_text data into reading_text
        DB::statement('UPDATE vocal_test_submissions SET reading_text = recitation_text WHERE reading_text IS NULL');

        // Set submitted_at for existing submissions
        DB::statement('UPDATE vocal_test_submissions SET submitted_at = created_at WHERE submitted_at IS NULL');

        // Now drop the old recitation_text column
        Schema::table('vocal_test_submissions', function (Blueprint $table) {
            $table->dropColumn('recitation_text');
        });
    }

    public function down(): void
    {
        Schema::table('vocal_test_submissions', function (Blueprint $table) {
            // Re-add recitation_text
            $table->longText('recitation_text')->nullable();
        });

        // Copy reading_text back to recitation_text
        DB::statement('UPDATE vocal_test_submissions SET recitation_text = reading_text WHERE recitation_text IS NULL');

        Schema::table('vocal_test_submissions', function (Blueprint $table) {
            // Drop foreign key BEFORE dropping the column
            $table->dropForeign(['vocal_test_prompt_id']);

            $table->dropColumn([
                'vocal_test_prompt_id',
                'reading_text',
                'audio_original_name',
                'audio_size',
                'duration_seconds',
                'status',
                'teacher_comment',
                'score',
                'submitted_at',
                'reviewed_at',
            ]);
        });
    }
};
