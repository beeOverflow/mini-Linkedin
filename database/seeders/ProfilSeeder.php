<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Profil;
use App\Models\User;
use App\Models\Competence;

class ProfilSeeder extends Seeder
{
    public function run(): void
    {
        $candidats = User::where('role', 'candidat')->get();
        $competences = Competence::all();

        foreach ($candidats as $user) {

            $profil = Profil::create([
                'user_id' => $user->id,
                'titre' => 'Developer',
                'bio' => 'Motivated developer',
                'localisation' => 'Casablanca',
                'disponible' => true,
            ]);

            // attach 2–3 competences with random niveau
            foreach ($competences->random(rand(2,3)) as $comp) {
                $profil->competences()->attach($comp->id, [
                    'niveau' => collect(['debutant', 'intermediaire', 'expert'])->random()
                ]);
            }
        }
    }
}
