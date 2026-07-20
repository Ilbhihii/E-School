<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $arabicId = DB::table('subjects')->whereRaw('LOWER(name) = ?', ['arabe'])->value('id');
        $quranId = DB::table('subjects')->whereRaw('LOWER(name) = ?', ['coran'])->value('id');

        if (!$arabicId || !$quranId) {
            return;
        }

        $this->assignLevel('alphabet', $arabicId, 1);
        $this->assignLevel('Lecture et communication', $arabicId, 2);
        $this->assignLevel('Maîtrise intermédiaire', $arabicId, 3);
        $this->assignLevel('Expression écrite et orale', $arabicId, 4);
        $this->assignLevel('Apprendre les règles', $quranId, 1);
        $this->assignLevel('Tajwid et Hifd', $quranId, 2);

        $arabicLevelIds = DB::table('levels')->where('subject_id', $arabicId)->pluck('id');
        $quranLevelIds = DB::table('levels')->where('subject_id', $quranId)->pluck('id');

        $this->syncClassSubjects($arabicLevelIds, $arabicId, $quranId);
        $this->syncClassSubjects($quranLevelIds, $quranId, $arabicId);
    }

    public function down(): void
    {
        $arabicId = DB::table('subjects')->whereRaw('LOWER(name) = ?', ['arabe'])->value('id');
        $quranId = DB::table('subjects')->whereRaw('LOWER(name) = ?', ['coran'])->value('id');

        if (!$arabicId || !$quranId) {
            return;
        }

        $arabicLevelIds = DB::table('levels')->where('subject_id', $arabicId)->pluck('id');
        $this->syncClassSubjects($arabicLevelIds, $quranId, $arabicId);

        DB::table('levels')
            ->whereIn('subject_id', [$arabicId, $quranId])
            ->update(['subject_id' => null, 'order' => 0]);
    }

    private function assignLevel(string $nameFragment, int $subjectId, int $order): void
    {
        DB::table('levels')
            ->where('name', 'like', '%' . $nameFragment . '%')
            ->update(['subject_id' => $subjectId, 'order' => $order]);
    }

    private function syncClassSubjects($levelIds, int $expectedSubjectId, int $wrongSubjectId): void
    {
        $classIds = DB::table('class_rooms')->whereIn('level_id', $levelIds)->pluck('id');

        foreach ($classIds as $classId) {
            DB::table('class_room_subject')->updateOrInsert(
                ['class_room_id' => $classId, 'subject_id' => $expectedSubjectId],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }

        DB::table('class_room_subject')
            ->whereIn('class_room_id', $classIds)
            ->where('subject_id', $wrongSubjectId)
            ->delete();
    }
};
