<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profil;
use App\Models\Competence;

class ProfilController extends Controller
{
    // POST /api/profil
    public function store(Request $request)
    {
        $user = auth()->user();

        // Un candidat ne peut créer son profil qu'une seule fois
        if ($user->profil) {
            return response()->json(['message' => 'Profil déjà existant'], 422);
        }

        $validated = $request->validate([
            'titre'        => 'required|string|max:255',
            'bio'          => 'nullable|string',
            'localisation' => 'nullable|string|max:255',
            'disponible'   => 'nullable|boolean',
        ]);

        $profil = Profil::create([
            'user_id'      => $user->id,
            'titre'        => $validated['titre'],
            'bio'          => $validated['bio'] ?? null,
            'localisation' => $validated['localisation'] ?? null,
            'disponible'   => $validated['disponible'] ?? true,
        ]);

        return response()->json($profil, 201);
    }

    // GET /api/profil
    public function show()
    {
        $profil = auth()->user()->profil;

        if (!$profil) {
            return response()->json(['message' => 'Profil introuvable'], 404);
        }

        $profil->load('competences');

        return response()->json($profil);
    }

    // PUT /api/profil
    public function update(Request $request)
    {
        $profil = auth()->user()->profil;

        if (!$profil) {
            return response()->json(['message' => 'Profil introuvable'], 404);
        }

        $validated = $request->validate([
            'titre'        => 'sometimes|string|max:255',
            'bio'          => 'nullable|string',
            'localisation' => 'nullable|string|max:255',
            'disponible'   => 'nullable|boolean',
        ]);

        $profil->update($validated);

        return response()->json($profil);
    }

    // POST /api/profil/competences
    public function addCompetence(Request $request)
    {
        $profil = auth()->user()->profil;

        if (!$profil) {
            return response()->json(['message' => 'Profil introuvable'], 404);
        }

        $validated = $request->validate([
            'competence_id' => 'required|exists:competences,id',
            'niveau'        => 'required|in:débutant,intermédiaire,expert',
        ]);

        // Vérifier si la compétence est déjà ajoutée
        if ($profil->competences()->where('competence_id', $validated['competence_id'])->exists()) {
            return response()->json(['message' => 'Compétence déjà ajoutée'], 422);
        }

        $profil->competences()->attach($validated['competence_id'], [
            'niveau' => $validated['niveau'],
        ]);

        return response()->json(['message' => 'Compétence ajoutée avec succès'], 201);
    }

    // DELETE /api/profil/competences/{competence}
    public function removeCompetence($competenceId)
    {
        $profil = auth()->user()->profil;

        if (!$profil) {
            return response()->json(['message' => 'Profil introuvable'], 404);
        }

        $competence = Competence::find($competenceId);

        if (!$competence) {
            return response()->json(['message' => 'Compétence introuvable'], 404);
        }

        $profil->competences()->detach($competenceId);

        return response()->json(['message' => 'Compétence retirée avec succès']);
    }
}