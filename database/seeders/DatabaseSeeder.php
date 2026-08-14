<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $seeders = [
            SubjectSeeder::class,
            LevelSeeder::class,
            ModuleSeeder::class,
            ClassSeeder::class,
            RoleSeeder::class,
            UserRoleSeeder::class,
            ArabicVocalTestPromptSeeder::class,
            QuranVocalTestPromptSeeder::class,
        ];

        /*
         * TestSeeder crée uniquement des données fictives via les
         * factories/Faker. Faker est une dépendance require-dev et n'est
         * normalement pas installée sur le VPS en production.
         */
        if (!app()->environment('production')) {
            $seeders[] = TestSeeder::class;
        }

        $this->call($seeders);
    }

}
