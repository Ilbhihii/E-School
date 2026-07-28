<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\Subject;
use App\Models\VocalTestPrompt;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $structure = [
            'Arabe' => [
                [
                    'name' => VocalTestPrompt::ARABIC_READING_WRITING,
                    'description' => 'Apprendre à lire et à écrire en arabe.',
                    'order' => 1,
                ],
                [
                    'name' => VocalTestPrompt::ARABIC_COMMUNICATION,
                    'description' => 'Apprendre à comprendre et à communiquer en arabe.',
                    'order' => 2,
                ],
            ],
            'Coran' => [
                [
                    'name' => VocalTestPrompt::QURAN_LEARNING_TAJWID,
                    'description' => 'Comprendre, appliquer les règles du tajwid et mémoriser.',
                    'order' => 1,
                ],
            ],
        ];

        foreach ($structure as $subjectName => $levels) {
            $subject = Subject::where('name', $subjectName)->first();

            if (!$subject) {
                $this->command->warn(
                    "Matière '{$subjectName}' introuvable."
                );
                continue;
            }

            foreach ($levels as $levelData) {
                Level::updateOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'name' => $levelData['name'],
                    ],
                    [
                        'description' => $levelData['description'],
                        'order' => $levelData['order'],
                    ]
                );
            }
        }

        $this->command->info(
            'Parcours créés : Lecture & Écriture, Communication, Apprentissage & Tajwid.'
        );
    }
}
