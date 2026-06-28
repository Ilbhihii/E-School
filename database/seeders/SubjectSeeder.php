<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        $islamicSubjects = [
            'Coran',
            'Tajwid',
            'Fiqh',
            'Aqida',
            'Hadith',
            'Sira',
            'Tafsir',
            'Langue Arabe Islamique',
        ];

        $subjects = [
            // Matières scolaires
            'Français',
            'Arabe',
            'Maths',
            'Physique',
            'SVT',
            'Histoire',
            'Anglais',
            'Administration',
            'Informatique',

            // Matières islamiques
            'Coran',
            'Tajwid',
            'Fiqh',
            'Aqida',
            'Hadith',
            'Sira',
            'Tafsir',
            'Langue Arabe Islamique',
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(
                ['name' => $subject],
                [
                    'type' => in_array($subject, $islamicSubjects) ? 'religieux' : 'scolaire',
                ]
            );
        }
    }
}