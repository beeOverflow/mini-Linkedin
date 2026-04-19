<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offre;

class OffreController extends Controller
{
    // GET /api/offres
    public function index(Request $request)
    {
        $query = Offre::where('actif', true)->with('user');

        // Filtre par localisation
        if ($request->has('localisation')) {
            $query->where('localisation', 'like', '%' . $request->localisation . '%');
        }

        // Filtre par type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Tri par date de création (desc par défaut)
        $query->orderBy('created_at', 'desc');

        $offres = $query->paginate(10);

        return response()->json($offres);
    }

    // GET /api/offres/{offre}
    public function show($id)
    {
        $offre = Offre::with('user')->find($id);

        if (!$offre) {
            return response()->json(['message' => 'Offre introuvable'], 404);
        }

        return response()->json($offre);
    }

    // POST /api/offres
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre'        => 'required|string|max:255',
            'description'  => 'required|string',
            'localisation' => 'required|string|max:255',
            'type'         => 'required|in:CDI,CDD,stage',
        ]);

        $offre = Offre::create([
            'user_id'      => auth()->id(),
            'titre'        => $validated['titre'],
            'description'  => $validated['description'],
            'localisation' => $validated['localisation'],
            'type'         => $validated['type'],
            'actif'        => true,
        ]);

        return response()->json($offre, 201);
    }

    // PUT /api/offres/{offre}
    public function update(Request $request, $id)
    {
        $offre = Offre::find($id);

        if (!$offre) {
            return response()->json(['message' => 'Offre introuvable'], 404);
        }

        // Vérifier ownership
        if ($offre->user_id !== auth()->id()) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $validated = $request->validate([
            'titre'        => 'sometimes|string|max:255',
            'description'  => 'sometimes|string',
            'localisation' => 'sometimes|string|max:255',
            'type'         => 'sometimes|in:CDI,CDD,stage',
        ]);

        $offre->update($validated);

        return response()->json($offre);
    }

    // DELETE /api/offres/{offre}
    public function destroy($id)
    {
        $offre = Offre::find($id);

        if (!$offre) {
            return response()->json(['message' => 'Offre introuvable'], 404);
        }

        // Vérifier ownership
        if ($offre->user_id !== auth()->id()) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $offre->delete();

        return response()->json(['message' => 'Offre supprimée avec succès']);
    }
}