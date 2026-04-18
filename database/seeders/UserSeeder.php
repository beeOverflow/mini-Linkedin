<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admins
        User::create([
            'name' => 'Admin 1',
            'email' => 'admin1@test.com',
            'password' => bcrypt('123456'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Admin 2',
            'email' => 'admin2@test.com',
            'password' => bcrypt('123456'),
            'role' => 'admin',
        ]);

        // Recruteurs
        User::factory()->count(5)->create([
            'role' => 'recruteur',
        ]);

        // Candidats
        User::factory()->count(10)->create([
            'role' => 'candidat',
        ]);
    }
}
