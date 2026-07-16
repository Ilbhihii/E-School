<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // Matières scolaires
            [
                'name' => 'Français',
                'type' => 'scolaire',
                'description' => 'Cours de français : grammaire, conjugaison, orthographe et littérature',
                'image' => null,
            ],
            [
                'name' => 'Arabe',
                'type' => 'scolaire',
                'description' => 'Apprentissage de la langue arabe : lecture, écriture, grammaire et communication',
                'image' => null,
            ],
            [
                'name' => 'Maths',
                'type' => 'scolaire',
                'description' => 'Mathématiques : calcul, géométrie, algèbre et résolution de problèmes',
                'image' => null,
            ],
            [
                'name' => 'Physique',
                'type' => 'scolaire',
                'description' => 'Physique et chimie : découverte des lois fondamentales de la matière',
                'image' => null,
            ],
            [
                'name' => 'SVT',
                'type' => 'scolaire',
                'description' => 'Sciences de la Vie et de la Terre : biologie et géologie',
                'image' => null,
            ],
            [
                'name' => 'Histoire',
                'type' => 'scolaire',
                'description' => 'Histoire et géographie : comprendre le passé et le monde actuel',
                'image' => null,
            ],
            [
                'name' => 'Anglais',
                'type' => 'scolaire',
                'description' => "Cours d'anglais : vocabulaire, grammaire et communication",
                'image' => null,
            ],
            [
                'name' => 'Administration',
                'type' => 'scolaire',
                'description' => 'Formation en administration et gestion',
                'image' => null,
            ],
            [
                'name' => 'Informatique',
                'type' => 'scolaire',
                'description' => "Initiation à l'informatique et aux technologies numériques",
                'image' => null,
            ],
            // Matières islamiques
            [
                'name' => 'Coran',
                'type' => 'religieux',
                'description' => 'Apprentissage du Coran : lecture, règles de tajwid et mémorisation',
                'image' => null,
            ],
            [
                'name' => 'Tajwid',
                'type' => 'religieux',
                'description' => 'Règles de récitation coranique et perfectionnement de la lecture',
                'image' => null,
            ],
            [
                'name' => 'Fiqh',
                'type' => 'religieux',
                'description' => "Jurisprudence islamique : les règles et principes de l'islam",
                'image' => null,
            ],
            [
                'name' => 'Aqida',
                'type' => 'religieux',
                'description' => 'Croyance islamique : les fondements de la foi',
                'image' => null,
            ],
            [
                'name' => 'Hadith',
                'type' => 'religieux',
                'description' => 'Étude des hadiths du Prophète Muhammad (PSL)',
                'image' => null,
            ],
            [
                'name' => 'Sira',
                'type' => 'religieux',
                'description' => 'Biographie du Prophète Muhammad (PSL) et histoire islamique',
                'image' => null,
            ],
            [
                'name' => 'Tafsir',
                'type' => 'religieux',
                'description' => 'Exégèse et interprétation des versets du Coran',
                'image' => null,
            ],
            [
                'name' => 'Langue Arabe Islamique',
                'type' => 'religieux',
                'description' => 'Langue arabe appliquée aux textes islamiques',
                'image' => null,
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::updateOrCreate(
                ['name' => $subject['name']],
                [
                    'type' => $subject['type'],
                    'description' => $subject['description'],
                    'image' => $subject['image'],
                ]
            );
        }
    }
}
