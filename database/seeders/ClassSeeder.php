<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;
use App\Models\ClassRoom;

class ClassSeeder extends Seeder
{
    /**
     * Structure exacte : pour chaque matière, les niveaux et leurs classes autorisées.
     * Hors Arabe et Coran, les niveaux reçoivent 3 classes par défaut.
     */
    private array $structure = [
        'Arabe' => [
            'Découverte de l\'alphabet'  => ['Débutant'],
            'Lecture et communication'   => ['Débutant', 'Intermédiaire', 'Avancée'],
            'Maîtrise intermédiaire'     => ['Intermédiaire'],
            'Expression écrite et orale' => ['Débutant', 'Intermédiaire', 'Avancée'],
        ],
        'Coran' => [
            'Apprendre les règles' => ['Débutant', 'Intermédiaire', 'Avancée'],
            'Tajwid et Hifd'       => ['Débutant', 'Intermédiaire', 'Avancée'],
        ],
    ];

    /** Classes par défaut pour les niveaux non listés */
    private array $defaultClasses = ['Débutant', 'Intermédiaire', 'Avancée'];

    public function run(): void
    {
        $levels = Level::with('subject')->get();

        foreach ($levels as $level) {
            $subjectName = $level->subject?->name ?? '';

            // Classes autorisées pour ce niveau
            $allowed = $this->structure[$subjectName][$level->name] ?? $this->defaultClasses;

            foreach ($allowed as $className) {
                ClassRoom::firstOrCreate(
                    [
                        'name'     => $className,
                        'level_id' => $level->id,
                    ]
                );
            }
        }

        $this->command->info('Classes créées selon la structure matière → niveau → classe.');
    }
}
