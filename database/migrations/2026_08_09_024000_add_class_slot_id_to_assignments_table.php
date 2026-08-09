<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('assignments', 'class_slot_id')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table
                    ->foreignId('class_slot_id')
                    ->nullable()
                    ->after('class_room_id')
                    ->constrained('class_slots')
                    ->nullOnDelete();

                $table->index(
                    ['user_id', 'subject_id', 'class_slot_id'],
                    'assignments_user_subject_slot_idx'
                );
            });
        }

        $this->backfillFromCourses();
    }

    public function down(): void
    {
        if (Schema::hasColumn('assignments', 'class_slot_id')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->dropIndex('assignments_user_subject_slot_idx');
                $table->dropConstrainedForeignId('class_slot_id');
            });
        }
    }

    private function backfillFromCourses(): void
    {
        if (
            !Schema::hasTable('class_slots')
            || !Schema::hasColumn('courses', 'slot_code')
        ) {
            return;
        }

        DB::table('assignments')
            ->whereNull('class_slot_id')
            ->whereNotNull('course_id')
            ->orderBy('id')
            ->chunkById(100, function ($rows) {
                foreach ($rows as $assignment) {
                    $course = DB::table('courses')
                        ->where('id', $assignment->course_id)
                        ->first();

                    if (!$course || trim((string) $course->slot_code) === '') {
                        continue;
                    }

                    $slotId = DB::table('class_slots')
                        ->where('subject_id', $course->subject_id)
                        ->where('level_id', $course->level_id)
                        ->where('class_id', $course->class_id)
                        ->whereRaw(
                            'UPPER(TRIM(code)) = ?',
                            [strtoupper(trim((string) $course->slot_code))]
                        )
                        ->where('is_active', true)
                        ->value('id');

                    if ($slotId) {
                        DB::table('assignments')
                            ->where('id', $assignment->id)
                            ->update([
                                'class_slot_id' => (int) $slotId,
                                'updated_at' => now(),
                            ]);
                    }
                }
            });
    }
};
