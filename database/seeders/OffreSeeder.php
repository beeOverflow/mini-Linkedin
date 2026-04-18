<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Offre;
use App\Models\User;

class OffreSeeder extends Seeder
{
    public function run(): void
    {
        $recruteurs = User::where('role', 'recruteur')->get();

        foreach ($recruteurs as $user) {
            Offre::factory()->count(rand(2,3))->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
