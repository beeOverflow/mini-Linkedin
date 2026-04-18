<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Candidature;
use App\Models\Profil;
use App\Models\Offre;

class CandidatureSeeder extends Seeder
{
    public function run(): void
    {
        $profils = Profil::all();
        $offres = Offre::all();

        foreach ($profils as $profil) {
            $offre = $offres->random();

            Candidature::create([
                'profil_id' => $profil->id,
                'offre_id' => $offre->id,
                'message' => 'Interested in this job',
                'statut' => 'en_attente',
            ]);
        }
    }
}
