<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Subject;
use App\Models\VocalTestPrompt;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $structure = [
            'Arabe' => [
                VocalTestPrompt::ARABIC_READING_WRITING,
                VocalTestPrompt::ARABIC_COMMUNICATION,
            ],
            'Coran' => [
                VocalTestPrompt::QURAN_LEARNING_TAJWID,
            ],
        ];

        foreach ($structure as $subjectName => $levelNames) {
            $subject = Subject::where('name', $subjectName)->first();

            if (!$subject) {
                continue;
            }

            $levels = Level::where('subject_id', $subject->id)
                ->whereIn('name', $levelNames)
                ->get();

            foreach ($levels as $level) {
                foreach (VocalTestPrompt::allowedClassNames() as $className) {
                    $classRoom = ClassRoom::firstOrCreate([
                        'level_id' => $level->id,
                        'name' => $className,
                    ]);

                    $classRoom->subjects()->syncWithoutDetaching([
                        $subject->id,
                    ]);
                }
            }
        }

        $this->command->info(
            'Classes créées : Débutant, Intermédiaire et Avancé.'
        );
    }
}
