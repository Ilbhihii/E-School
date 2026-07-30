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
                'levels' => [
                    VocalTestPrompt::ARABIC_READING_WRITING,
                    VocalTestPrompt::ARABIC_COMMUNICATION,
                ],
                'items' => VocalTestPrompt::allowedClassNames(),
            ],

            'Coran' => [
                'levels' => [
                    VocalTestPrompt::QURAN_LEARNING_TAJWID,
                ],
                'items' => VocalTestPrompt::allowedClassNames(),
            ],

            /*
             * Pour Soutien Lycée, les éléments sont stockés dans
             * class_rooms afin de conserver l'architecture existante.
             * L'interface les présente comme des matières.
             */
            'Soutien Lycée' => [
                'levels' => ['BAC'],
                'items' => [
                    'Mathématiques',
                    'Physique-Chimie',
                ],
            ],
        ];

        foreach ($structure as $subjectName => $config) {
            $subject = Subject::where(
                'name',
                $subjectName
            )->first();

            if (!$subject) {
                $this->command->warn(
                    "Matière '{$subjectName}' introuvable."
                );
                continue;
            }

            $levels = Level::query()
                ->where('subject_id', $subject->id)
                ->whereIn('name', $config['levels'])
                ->get();

            foreach ($levels as $level) {
                foreach ($config['items'] as $itemName) {
                    $classRoom = ClassRoom::firstOrCreate([
                        'level_id' => $level->id,
                        'name' => $itemName,
                    ]);

                    $classRoom->subjects()
                        ->syncWithoutDetaching([
                            $subject->id,
                        ]);
                }
            }
        }

        $this->command->info(
            'Classes et matières créées : '
            . 'Débutant, Intermédiaire, Avancé, '
            . 'Mathématiques et Physique-Chimie.'
        );
    }
}
