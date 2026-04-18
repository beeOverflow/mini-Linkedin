<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Competence;
class CompetenceSeeder extends Seeder
{

public function run(): void
{
    $competences = [
        ['nom' => 'Laravel', 'categorie' => 'Backend'],
        ['nom' => 'PHP', 'categorie' => 'Backend'],
        ['nom' => 'JavaScript', 'categorie' => 'Frontend'],
        ['nom' => 'React', 'categorie' => 'Frontend'],
        ['nom' => 'Docker', 'categorie' => 'DevOps'],
    ];

    foreach ($competences as $comp) {
        Competence::create($comp);
    }
}
}
