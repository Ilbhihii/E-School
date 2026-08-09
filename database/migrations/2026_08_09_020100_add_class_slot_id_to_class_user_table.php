<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasTable('class_user')
            && !Schema::hasColumn(
                'class_user',
                'class_slot_id'
            )
        ) {
            $afterColumn =
                Schema::hasColumn(
                    'class_user',
                    'subject_id'
                )
                    ? 'subject_id'
                    : 'class_id';

            Schema::table(
                'class_user',
                function (
                    Blueprint $table
                ) use ($afterColumn) {
                    $table
                        ->foreignId('class_slot_id')
                        ->nullable()
                        ->after($afterColumn)
                        ->constrained('class_slots')
                        ->nullOnDelete();

                    $table->index(
                        [
                            'user_id',
                            'subject_id',
                            'class_slot_id',
                        ],
                        'class_user_student_slot_idx'
                    );
                }
            );
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('class_user')
            && Schema::hasColumn(
                'class_user',
                'class_slot_id'
            )
        ) {
            Schema::table(
                'class_user',
                function (Blueprint $table) {
                    $table->dropIndex(
                        'class_user_student_slot_idx'
                    );

                    $table->dropConstrainedForeignId(
                        'class_slot_id'
                    );
                }
            );
        }
    }
};
