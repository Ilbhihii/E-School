<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $newLevels = [
            [
                'name' => 'N1 Apprendre l\'arabe',
                'description' => 'Apprentissage de la langue arabe pour débutants',
            ],
            [
                'name' => 'N2 Apprendre à lire',
                'description' => 'Apprentissage de la lecture en arabe',
            ],
            [
                'name' => 'N3 Apprendre les règles de tajwid',
                'description' => 'Apprentissage des règles de récitation coranique',
            ],
            [
                'name' => 'N4 Sciences de l\'islam',
                'description' => 'Étude approfondie des sciences islamiques',
            ],
        ];

        $newNames = array_column($newLevels, 'name');

        // Supprimer les anciens niveaux qui ne sont plus dans la liste
        Level::whereNotIn('name', $newNames)->delete();

        // Créer ou mettre à jour les nouveaux niveaux
        foreach ($newLevels as $level) {
            Level::updateOrCreate(
                ['name' => $level['name']],
                ['description' => $level['description']]
            );
        }
    }
}
