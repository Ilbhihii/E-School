<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Level;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────────────
        // 📖 Coran
        // ──────────────────────────────────────────────
        $coran = Subject::where('name', 'Coran')->first();

        if ($coran) {
            // Niveau 1 – Apprendre les règles
            $n1Coran = Level::where('name', 'Niveau 1 – Apprendre les règles')
                ->where('subject_id', $coran->id)
                ->first();

            if ($n1Coran) {
                $modulesCoranN1 = [
                    ['title' => 'Les lettres arabes',  'description' => 'Introduction aux lettres de l\'alphabet arabe',  'order' => 1],
                    ['title' => 'Les harakats',         'description' => 'Les voyelles courtes : fatha, kasra, damma',  'order' => 2],
                    ['title' => 'Le soukoun',           'description' => 'L\'absence de voyelle et son utilisation',  'order' => 3],
                    ['title' => 'La chadda',            'description' => 'La gémination des lettres',  'order' => 4],
                    ['title' => 'Les règles de base',   'description' => 'Règles fondamentales de la lecture coranique',  'order' => 5],
                ];

                foreach ($modulesCoranN1 as $module) {
                    Module::updateOrCreate(
                        ['level_id' => $n1Coran->id, 'title' => $module['title']],
                        ['description' => $module['description'], 'order' => $module['order']]
                    );
                }
            }

            // Niveau 2 – Tajwid et Hifd
            $n2Coran = Level::where('name', 'Niveau 2 – Tajwid et Hifd')
                ->where('subject_id', $coran->id)
                ->first();

            if ($n2Coran) {
                $modulesCoranN2 = [
                    ['title' => 'Makharij',           'description' => 'Les points d\'articulation des lettres',  'order' => 1],
                    ['title' => 'Sifat',              'description' => 'Les caractéristiques des lettres',  'order' => 2],
                    ['title' => 'Madd',               'description' => 'Les règles de prolongation',  'order' => 3],
                    ['title' => 'Ghunna',             'description' => 'La nasalisation',  'order' => 4],
                    ['title' => 'Ikhfaa',             'description' => 'Le voilement',  'order' => 5],
                    ['title' => 'Idgham',             'description' => 'La fusion des lettres',  'order' => 6],
                    ['title' => 'Hifd Juz Amma',      'description' => 'Mémorisation du Juz Amma',  'order' => 7],
                    ['title' => 'Révision',           'description' => 'Révision générale des règles de tajwid',  'order' => 8],
                ];

                foreach ($modulesCoranN2 as $module) {
                    Module::updateOrCreate(
                        ['level_id' => $n2Coran->id, 'title' => $module['title']],
                        ['description' => $module['description'], 'order' => $module['order']]
                    );
                }
            }
        }

        // ──────────────────────────────────────────────
        // 📚 Arabe
        // ──────────────────────────────────────────────
        $arabe = Subject::where('name', 'Arabe')->first();

        if ($arabe) {
            // Niveau 1 – Découverte de l'alphabet
            $n1Arabe = Level::where('name', 'Niveau 1 – Découverte de l\'alphabet')
                ->where('subject_id', $arabe->id)
                ->first();

            if ($n1Arabe) {
                $modulesArabeN1 = [
                    ['title' => 'Alphabet',       'description' => 'Les 28 lettres arabes et leur prononciation',  'order' => 1],
                    ['title' => 'Les formes',      'description' => 'Les formes des lettres (début, milieu, fin, isolée)',  'order' => 2],
                    ['title' => 'Harakats',        'description' => 'Les voyelles courtes : fatha, kasra, damma',  'order' => 3],
                    ['title' => 'Lecture',         'description' => 'Lecture de syllabes et de mots simples',  'order' => 4],
                    ['title' => 'Écriture',        'description' => 'Écriture des lettres et des premiers mots',  'order' => 5],
                    ['title' => 'Vocabulaire',     'description' => 'Vocabulaire de base (couleurs, chiffres, famille)',  'order' => 6],
                    ['title' => 'Exercices',       'description' => 'Exercices d\'application',  'order' => 7],
                    ['title' => 'Quiz',            'description' => 'Quiz de mi-parcours',  'order' => 8],
                    ['title' => 'Test final',      'description' => 'Évaluation de fin de niveau 1',  'order' => 9],
                ];

                foreach ($modulesArabeN1 as $module) {
                    Module::updateOrCreate(
                        ['level_id' => $n1Arabe->id, 'title' => $module['title']],
                        ['description' => $module['description'], 'order' => $module['order']]
                    );
                }
            }

            // Niveau 2 – Lecture et communication
            $n2Arabe = Level::where('name', 'Niveau 2 – Lecture et communication')
                ->where('subject_id', $arabe->id)
                ->first();

            if ($n2Arabe) {
                $modulesArabeN2 = [
                    ['title' => 'Lecture',          'description' => 'Lecture fluide de petits textes',  'order' => 1],
                    ['title' => 'Pronoms',          'description' => 'Pronoms personnels et construction de phrases',  'order' => 2],
                    ['title' => 'Verbes',           'description' => 'Les verbes au présent',  'order' => 3],
                    ['title' => 'Dialogues',        'description' => 'Dialogues du quotidien',  'order' => 4],
                    ['title' => 'Vocabulaire',      'description' => 'Vocabulaire : école, maison, nourriture, transports',  'order' => 5],
                    ['title' => 'Exercices',        'description' => 'Exercices de communication et grammaire',  'order' => 6],
                    ['title' => 'Test',             'description' => 'Évaluation de fin de niveau 2',  'order' => 7],
                ];

                foreach ($modulesArabeN2 as $module) {
                    Module::updateOrCreate(
                        ['level_id' => $n2Arabe->id, 'title' => $module['title']],
                        ['description' => $module['description'], 'order' => $module['order']]
                    );
                }
            }

            // Niveau 3 – Maîtrise intermédiaire
            $n3Arabe = Level::where('name', 'Niveau 3 – Maîtrise intermédiaire')
                ->where('subject_id', $arabe->id)
                ->first();

            if ($n3Arabe) {
                $modulesArabeN3 = [
                    ['title' => 'Présent',           'description' => 'Conjugaison des verbes au présent',  'order' => 1],
                    ['title' => 'Passé',             'description' => 'Conjugaison des verbes au passé',  'order' => 2],
                    ['title' => 'Futur',             'description' => 'Expression du futur',  'order' => 3],
                    ['title' => 'Compréhension',     'description' => 'Compréhension de textes plus longs',  'order' => 4],
                    ['title' => 'Rédaction',         'description' => 'Rédaction guidée et résumés',  'order' => 5],
                    ['title' => 'Exercices',         'description' => 'Exercices de conjugaison et vocabulaire',  'order' => 6],
                    ['title' => 'Test',              'description' => 'Évaluation de fin de niveau 3',  'order' => 7],
                ];

                foreach ($modulesArabeN3 as $module) {
                    Module::updateOrCreate(
                        ['level_id' => $n3Arabe->id, 'title' => $module['title']],
                        ['description' => $module['description'], 'order' => $module['order']]
                    );
                }
            }

            // Niveau 4 – Expression écrite et orale avancée
            $n4Arabe = Level::where('name', 'Niveau 4 – Expression écrite et orale avancée')
                ->where('subject_id', $arabe->id)
                ->first();

            if ($n4Arabe) {
                $modulesArabeN4 = [
                    ['title' => 'Grammaire',         'description' => 'Grammaire avancée et styles d\'expression',  'order' => 1],
                    ['title' => 'Rédaction',         'description' => 'Rédaction d\'articles et textes structurés',  'order' => 2],
                    ['title' => 'Débat',             'description' => 'Argumentation et débat',  'order' => 3],
                    ['title' => 'Dictée',            'description' => 'Dictées et corrections',  'order' => 4],
                    ['title' => 'Présentation',      'description' => 'Présentations orales',  'order' => 5],
                    ['title' => 'Test final',        'description' => 'Évaluation finale du niveau 4',  'order' => 6],
                ];

                foreach ($modulesArabeN4 as $module) {
                    Module::updateOrCreate(
                        ['level_id' => $n4Arabe->id, 'title' => $module['title']],
                        ['description' => $module['description'], 'order' => $module['order']]
                    );
                }
            }
        }
    }
}
