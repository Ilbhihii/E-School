<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $newReadingText = "كَتَبَ
ذَهَبَ
جَمَلٌ
قَمَرٌ
وَلَدٌ
كِتَابٌ
سَمَكٌ
مَلِكٌ
رَجُلٌ
زَهَرٌ";

    private string $oldReadingText =
        'مِنْ هِوَايَاتِي الْمُفَضَّلَةِ '
        . 'قِرَاءَةُ الْكُتُبِ وَرَسْمُ اللَّوْحَاتِ '
        . 'وَمُمَارَسَةُ الرِّيَاضَةِ. '
        . 'أَقْرَأُ كُلَّ يَوْمٍ قَبْلَ النَّوْمِ، '
        . 'وَأُحَاوِلُ أَنْ أَكْتُبَ مَا تَعَلَّمْتُهُ '
        . 'فِي دَفْتَرِي.';

    public function up(): void
    {
        if (
            !Schema::hasTable('subjects')
            || !Schema::hasTable('levels')
            || !Schema::hasTable('class_rooms')
            || !Schema::hasTable('vocal_test_prompts')
        ) {
            return;
        }

        $subjectId = DB::table('subjects')
            ->where('name', 'Arabe')
            ->value('id');

        if (!$subjectId) {
            return;
        }

        $levelId = DB::table('levels')
            ->where('subject_id', $subjectId)
            ->where('name', 'Lecture & Écriture')
            ->value('id');

        if (!$levelId) {
            return;
        }

        $classId = DB::table('class_rooms')
            ->where('level_id', $levelId)
            ->where('name', 'Intermédiaire')
            ->value('id');

        if (!$classId) {
            return;
        }

        DB::table('vocal_test_prompts')
            ->where('subject_id', $subjectId)
            ->where('level_id', $levelId)
            ->where('class_id', $classId)
            ->update([
                'reading_text' =>
                    $this->newReadingText,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        if (
            !Schema::hasTable('subjects')
            || !Schema::hasTable('levels')
            || !Schema::hasTable('class_rooms')
            || !Schema::hasTable('vocal_test_prompts')
        ) {
            return;
        }

        $subjectId = DB::table('subjects')
            ->where('name', 'Arabe')
            ->value('id');

        if (!$subjectId) {
            return;
        }

        $levelId = DB::table('levels')
            ->where('subject_id', $subjectId)
            ->where('name', 'Lecture & Écriture')
            ->value('id');

        if (!$levelId) {
            return;
        }

        $classId = DB::table('class_rooms')
            ->where('level_id', $levelId)
            ->where('name', 'Intermédiaire')
            ->value('id');

        if (!$classId) {
            return;
        }

        DB::table('vocal_test_prompts')
            ->where('subject_id', $subjectId)
            ->where('level_id', $levelId)
            ->where('class_id', $classId)
            ->update([
                'reading_text' =>
                    $this->oldReadingText,
                'updated_at' => now(),
            ]);
    }
};
