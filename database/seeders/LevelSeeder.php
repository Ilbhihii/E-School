<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;
use App\Models\Subject;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────────────
        // 📖 Niveaux liés au Coran
        // ──────────────────────────────────────────────
        $coran = Subject::where('name', 'Coran')->first();
        if ($coran) {
            $coranLevels = [
                [
                    'name' => 'Apprendre les règles',
                    'description' => 'Apprendre les bases de la lecture correcte du Coran',
                    'subject_id' => $coran->id,
                    'order' => 1,
                ],
                [
                    'name' => 'Tajwid et Hifd',
                    'description' => 'Perfectionnement en tajwid et mémorisation du Coran',
                    'subject_id' => $coran->id,
                    'order' => 2,
                ],
            ];

            foreach ($coranLevels as $level) {
                Level::updateOrCreate(
                    ['name' => $level['name'], 'subject_id' => $level['subject_id']],
                    ['description' => $level['description'], 'order' => $level['order']]
                );
            }
        }

        // ──────────────────────────────────────────────
        // 📚 Niveaux liés à l'Arabe
        // ──────────────────────────────────────────────
        $arabe = Subject::where('name', 'Arabe')->first();
        if ($arabe) {
            $arabeLevels = [
                [
                    'name' => 'Découverte de l\'alphabet',
                    'description' => 'Lire et écrire l\'alphabet arabe (Débutant)',
                    'subject_id' => $arabe->id,
                    'order' => 1,
                ],
                [
                    'name' => 'Lecture et communication',
                    'description' => 'Comprendre et produire des phrases simples (Élémentaire)',
                    'subject_id' => $arabe->id,
                    'order' => 2,
                ],
                [
                    'name' => 'Maîtrise intermédiaire',
                    'description' => 'S\'exprimer avec aisance sur des sujets variés',
                    'subject_id' => $arabe->id,
                    'order' => 3,
                ],
                [
                    'name' => 'Expression écrite et orale',
                    'description' => 'Rédiger des textes et communiquer de manière autonome',
                    'subject_id' => $arabe->id,
                    'order' => 4,
                ],
            ];

            foreach ($arabeLevels as $level) {
                Level::updateOrCreate(
                    ['name' => $level['name'], 'subject_id' => $level['subject_id']],
                    ['description' => $level['description'], 'order' => $level['order']]
                );
            }
        }

    }
}
