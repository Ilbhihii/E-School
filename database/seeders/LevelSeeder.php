<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            '1ère année primaire',
            '2ème année primaire',
            '3ème année primaire',
            '4ème année primaire',
            '5ème année primaire',
            '6ème année primaire',
            '1ère année collège',
            '2ème année collège',
            '3ème année collège',
            '1ère année lycée',
            '2ème année lycée',
            '3ème année lycée',
        ];

        foreach ($levels as $name) {
            Level::firstOrCreate(
                ['name' => $name],
                [
                    'description' => 'Niveau éducatif standard',
                    'subject_id' => 1
                ]
            );
        }
    }
}
