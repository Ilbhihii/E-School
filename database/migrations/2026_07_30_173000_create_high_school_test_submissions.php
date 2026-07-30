<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable(
                'high_school_test_submissions'
            )
        ) {
            Schema::create(
                'high_school_test_submissions',
                function (Blueprint $table) {
                    $table->id();

                    $table->foreignId('user_id')
                        ->constrained('users')
                        ->cascadeOnDelete();

                    $table->foreignId('subject_id')
                        ->constrained('subjects')
                        ->cascadeOnDelete();

                    $table->foreignId('level_id')
                        ->constrained('levels')
                        ->cascadeOnDelete();

                    $table->foreignId('class_id')
                        ->constrained('class_rooms')
                        ->cascadeOnDelete();

                    $table->string('test_key', 80);
                    $table->string('test_title');
                    $table->json('questions_snapshot');
                    $table->json('answer_images');

                    $table->string('status', 30)
                        ->default('submitted');

                    $table->unsignedTinyInteger('score')
                        ->nullable();

                    $table->text('teacher_comment')
                        ->nullable();

                    $table->timestamp('submitted_at')
                        ->nullable();

                    $table->timestamp('reviewed_at')
                        ->nullable();

                    $table->timestamp('consumed_at')
                        ->nullable();

                    $table->timestamps();

                    $table->index([
                        'user_id',
                        'status',
                    ]);

                    $table->index([
                        'subject_id',
                        'level_id',
                        'class_id',
                    ], 'high_school_test_path_index');
                }
            );
        }

        if (
            Schema::hasTable('test_appointments')
            && !Schema::hasColumn(
                'test_appointments',
                'high_school_test_submission_id'
            )
        ) {
            Schema::table(
                'test_appointments',
                function (Blueprint $table) {
                    $table
                        ->foreignId(
                            'high_school_test_submission_id'
                        )
                        ->nullable()
                        ->unique()
                        ->constrained(
                            'high_school_test_submissions'
                        )
                        ->nullOnDelete();
                }
            );
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('test_appointments')
            && Schema::hasColumn(
                'test_appointments',
                'high_school_test_submission_id'
            )
        ) {
            Schema::table(
                'test_appointments',
                function (Blueprint $table) {
                    $table->dropForeign([
                        'high_school_test_submission_id',
                    ]);

                    $table->dropUnique([
                        'high_school_test_submission_id',
                    ]);

                    $table->dropColumn(
                        'high_school_test_submission_id'
                    );
                }
            );
        }

        Schema::dropIfExists(
            'high_school_test_submissions'
        );
    }
};
