<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DisableBeginnerArabicVocalTests extends Migration
{
    public function up(): void
    {
        $this->setExcludedPromptsActive(false);
    }

    public function down(): void
    {
        $this->setExcludedPromptsActive(true);
    }

    private function setExcludedPromptsActive(bool $isActive): void
    {
        $arabicSubject = DB::table('subjects')
            ->whereRaw('LOWER(name) = ?', ['arabe'])
            ->first();

        if (!$arabicSubject) {
            return;
        }

        $prompts = DB::table('vocal_test_prompts as prompts')
            ->join('levels', 'levels.id', '=', 'prompts.level_id')
            ->join('class_rooms', 'class_rooms.id', '=', 'prompts.class_id')
            ->where('prompts.subject_id', $arabicSubject->id)
            ->select([
                'prompts.id',
                'levels.name as level_name',
                'class_rooms.name as class_name',
            ])
            ->get();

        $excludedLevels = [
            'decouverte de l alphabet',
            'lecture et communication',
            'expression ecrite et orale',
        ];

        foreach ($prompts as $prompt) {
            $levelName = $this->normalizeName($prompt->level_name);
            $className = $this->normalizeName($prompt->class_name);

            if (
                $className === 'debutant'
                && in_array($levelName, $excludedLevels, true)
            ) {
                DB::table('vocal_test_prompts')
                    ->where('id', $prompt->id)
                    ->update([
                        'is_active' => $isActive,
                        'updated_at' => now(),
                    ]);
            }
        }
    }

    private function normalizeName(?string $value): string
    {
        $value = str_replace([
            '’',
            "'",
            '-',
            '_',
        ], ' ', (string) $value);

        $value = Str::lower(Str::ascii($value));

        return trim((string) preg_replace('/\s+/', ' ', $value));
    }
}