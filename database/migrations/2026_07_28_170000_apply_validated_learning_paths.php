<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable('subjects')
            || !Schema::hasTable('levels')
            || !Schema::hasTable('class_rooms')
        ) {
            return;
        }

        $structure = [
            'Arabe' => [
                [
                    'name' => 'Lecture & Écriture',
                    'description' => 'Apprendre à lire et à écrire en arabe.',
                    'order' => 1,
                ],
                [
                    'name' => 'Communication',
                    'description' => 'Apprendre à comprendre et à communiquer en arabe.',
                    'order' => 2,
                ],
            ],
            'Coran' => [
                [
                    'name' => 'Apprentissage & Tajwid',
                    'description' => 'Comprendre, appliquer les règles du tajwid et mémoriser.',
                    'order' => 1,
                ],
            ],
        ];

        $classNames = [
            'Débutant',
            'Intermédiaire',
            'Avancé',
        ];

        foreach ($structure as $subjectName => $levels) {
            $subject = DB::table('subjects')
                ->where('name', $subjectName)
                ->first();

            if (!$subject) {
                continue;
            }

            foreach ($levels as $levelData) {
                $level = DB::table('levels')
                    ->where('subject_id', $subject->id)
                    ->where('name', $levelData['name'])
                    ->first();

                if ($level) {
                    DB::table('levels')
                        ->where('id', $level->id)
                        ->update([
                            'description' => $levelData['description'],
                            'order' => $levelData['order'],
                            'updated_at' => now(),
                        ]);

                    $levelId = $level->id;
                } else {
                    $levelId = DB::table('levels')->insertGetId([
                        'subject_id' => $subject->id,
                        'name' => $levelData['name'],
                        'description' => $levelData['description'],
                        'order' => $levelData['order'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                foreach ($classNames as $className) {
                    $classRoom = DB::table('class_rooms')
                        ->where('level_id', $levelId)
                        ->where('name', $className)
                        ->first();

                    if ($classRoom) {
                        $classId = $classRoom->id;
                    } else {
                        $classId = DB::table('class_rooms')
                            ->insertGetId([
                                'level_id' => $levelId,
                                'name' => $className,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                    }

                    if (Schema::hasTable('class_room_subject')) {
                        DB::table('class_room_subject')->updateOrInsert(
                            [
                                'class_room_id' => $classId,
                                'subject_id' => $subject->id,
                            ],
                            [
                                'updated_at' => now(),
                                'created_at' => now(),
                            ]
                        );
                    }
                }
            }
        }

        $this->disableOldAndBeginnerPrompts();
    }

    public function down(): void
    {
        // Les anciens parcours ne sont pas restaurés automatiquement afin
        // de ne pas réactiver des tests devenus obsolètes.
    }

    private function disableOldAndBeginnerPrompts(): void
    {
        if (!Schema::hasTable('vocal_test_prompts')) {
            return;
        }

        $targets = [
            'Arabe' => [
                'Lecture & Écriture',
                'Communication',
            ],
            'Coran' => [
                'Apprentissage & Tajwid',
            ],
        ];

        foreach ($targets as $subjectName => $allowedLevels) {
            $subject = DB::table('subjects')
                ->where('name', $subjectName)
                ->first();

            if (!$subject) {
                continue;
            }

            $promptRows = DB::table('vocal_test_prompts as prompts')
                ->join('levels', 'levels.id', '=', 'prompts.level_id')
                ->join(
                    'class_rooms',
                    'class_rooms.id',
                    '=',
                    'prompts.class_id'
                )
                ->where('prompts.subject_id', $subject->id)
                ->select([
                    'prompts.id',
                    'levels.name as level_name',
                    'class_rooms.name as class_name',
                ])
                ->get();

            foreach ($promptRows as $prompt) {
                $isAllowedLevel = in_array(
                    $prompt->level_name,
                    $allowedLevels,
                    true
                );

                $isArabicBeginner = $subjectName === 'Arabe'
                    && $prompt->class_name === 'Débutant';

                if (!$isAllowedLevel || $isArabicBeginner) {
                    DB::table('vocal_test_prompts')
                        ->where('id', $prompt->id)
                        ->update([
                            'is_active' => false,
                            'updated_at' => now(),
                        ]);
                }
            }
        }
    }
};