<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $subjects = [
            'Français',
            'Arabe',
            'Maths',
            'Physique',
            'SVT',
            'Histoire',
            'Anglais',
            'Administration',
            'Informatique'
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(
                ['name' => $subject],
                [
                    'type' => in_array($subject, ['Arabe', 'Français']) ? 'religieux' : 'scolaire',
                ]
            );
        }
    }
}
