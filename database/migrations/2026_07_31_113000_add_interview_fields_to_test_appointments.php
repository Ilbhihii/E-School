<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('test_appointments')) {
            return;
        }

        $needsUser =
            !Schema::hasColumn(
                'test_appointments',
                'user_id'
            );

        $needsSubject =
            !Schema::hasColumn(
                'test_appointments',
                'subject_id'
            );

        $needsLevel =
            !Schema::hasColumn(
                'test_appointments',
                'level_id'
            );

        $needsClass =
            !Schema::hasColumn(
                'test_appointments',
                'class_id'
            );

        $needsMethod =
            !Schema::hasColumn(
                'test_appointments',
                'interview_method'
            );

        $needsDate =
            !Schema::hasColumn(
                'test_appointments',
                'preferred_date'
            );

        $needsTime =
            !Schema::hasColumn(
                'test_appointments',
                'preferred_time'
            );

        $needsNotes =
            !Schema::hasColumn(
                'test_appointments',
                'notes'
            );

        Schema::table(
            'test_appointments',
            function (Blueprint $table) use (
                $needsUser,
                $needsSubject,
                $needsLevel,
                $needsClass,
                $needsMethod,
                $needsDate,
                $needsTime,
                $needsNotes
            ) {
                if ($needsUser) {
                    $table->foreignId('user_id')
                        ->nullable()
                        ->after('id')
                        ->constrained('users')
                        ->nullOnDelete();
                }

                if ($needsSubject) {
                    $table->foreignId('subject_id')
                        ->nullable()
                        ->after('type')
                        ->constrained('subjects')
                        ->nullOnDelete();
                }

                if ($needsLevel) {
                    $table->foreignId('level_id')
                        ->nullable()
                        ->after('subject_id')
                        ->constrained('levels')
                        ->nullOnDelete();
                }

                if ($needsClass) {
                    $table->foreignId('class_id')
                        ->nullable()
                        ->after('level_id')
                        ->constrained('class_rooms')
                        ->nullOnDelete();
                }

                if ($needsMethod) {
                    $table->string(
                        'interview_method',
                        30
                    )
                        ->nullable()
                        ->after('class_id');
                }

                if ($needsDate) {
                    $table->date('preferred_date')
                        ->nullable()
                        ->after('interview_method');
                }

                if ($needsTime) {
                    $table->time('preferred_time')
                        ->nullable()
                        ->after('preferred_date');
                }

                if ($needsNotes) {
                    $table->text('notes')
                        ->nullable()
                        ->after('preferred_time');
                }
            }
        );
    }

    public function down(): void
    {
        if (!Schema::hasTable('test_appointments')) {
            return;
        }

        $foreignColumns = [
            'user_id',
            'subject_id',
            'level_id',
            'class_id',
        ];

        foreach ($foreignColumns as $column) {
            if (
                Schema::hasColumn(
                    'test_appointments',
                    $column
                )
            ) {
                Schema::table(
                    'test_appointments',
                    function (
                        Blueprint $table
                    ) use ($column) {
                        $table->dropForeign([$column]);
                    }
                );
            }
        }

        $columns = [
            'user_id',
            'subject_id',
            'level_id',
            'class_id',
            'interview_method',
            'preferred_date',
            'preferred_time',
            'notes',
        ];

        $existingColumns = array_values(
            array_filter(
                $columns,
                fn (string $column): bool =>
                    Schema::hasColumn(
                        'test_appointments',
                        $column
                    )
            )
        );

        if (!empty($existingColumns)) {
            Schema::table(
                'test_appointments',
                function (
                    Blueprint $table
                ) use ($existingColumns) {
                    $table->dropColumn(
                        $existingColumns
                    );
                }
            );
        }
    }
};
