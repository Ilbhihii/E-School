<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $rolesMap = [
            'admin' => 'admin',
            'prof' => 'prof',
            'student' => 'student',
        ];

        User::chunk(100, function ($users) use ($rolesMap) {
            foreach ($users as $user) {
                if (isset($rolesMap[$user->role])) {
                    $user->assignRole($rolesMap[$user->role]);
                }
            }
        });
    }
}

