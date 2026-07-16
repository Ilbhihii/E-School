<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;
use App\Models\ClassRoom;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $standardClasses = [
            'Débutant A1',
            'Débutant A2',
            'Intermédiaire B1',
            'Intermédiaire B2',
            'Avancé C1',
            'Avancé C2',
        ];

        $levels = Level::all();

        foreach ($levels as $level) {
            foreach ($standardClasses as $className) {
                ClassRoom::firstOrCreate(
                    [
                        'name' => $className,
                        'level_id' => $level->id,
                    ]
                );
            }
        }

        $this->command->info('Classes standards créées pour tous les niveaux.');
    }
}
