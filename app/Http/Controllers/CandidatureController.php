<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offre;
use App\Models\Candidature;

class CandidatureController extends Controller
{
    // POST /api/offres/{offre}/candidater
    public function postuler(Request $request, $offreId)
    {
        $offre = Offre::find($offreId);

        if (!$offre || !$offre->actif) {
            return response()->json(['message' => 'Offre introuvable ou inactive'], 404);
        }

        $profil = auth()->user()->profil;

        if (!$profil) {
            return response()->json(['message' => 'Vous devez créer un profil avant de postuler'], 422);
        }

        // Vérifier si le candidat a déjà postulé
        $dejaPostule = Candidature::where('offre_id', $offreId)
            ->where('profil_id', $profil->id)
            ->exists();

        if ($dejaPostule) {
            return response()->json(['message' => 'Vous avez déjà postulé à cette offre'], 422);
        }

        $validated = $request->validate([
            'message' => 'nullable|string',
        ]);

        $candidature = Candidature::create([
            'offre_id'  => $offreId,
            'profil_id' => $profil->id,
            'message'   => $validated['message'] ?? null,
            'statut'    => 'en_attente',
        ]);

        return response()->json($candidature, 201);
    }

    // GET /api/mes-candidatures
    public function mesCandidatures()
    {
        $profil = auth()->user()->profil;

        if (!$profil) {
            return response()->json(['message' => 'Profil introuvable'], 404);
        }

        $candidatures = Candidature::with('offre')
            ->where('profil_id', $profil->id)
            ->get();

        return response()->json($candidatures);
    }

    // GET /api/offres/{offre}/candidatures
    public function candidaturesRecues($offreId)
    {
        $offre = Offre::find($offreId);

        if (!$offre) {
            return response()->json(['message' => 'Offre introuvable'], 404);
        }

        // Vérifier ownership
        if ($offre->user_id !== auth()->id()) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $candidatures = Candidature::with('profil.user')
            ->where('offre_id', $offreId)
            ->get();

        return response()->json($candidatures);
    }

    // PATCH /api/candidatures/{candidature}/statut
    public function changerStatut(Request $request, $candidatureId)
    {
        $candidature = Candidature::with('offre')->find($candidatureId);

        if (!$candidature) {
            return response()->json(['message' => 'Candidature introuvable'], 404);
        }

        // Vérifier que le recruteur est propriétaire de l'offre
        if ($candidature->offre->user_id !== auth()->id()) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $validated = $request->validate([
            'statut' => 'required|in:en_attente,acceptee,refusee',
        ]);

        $candidature->update(['statut' => $validated['statut']]);

        return response()->json($candidature);
    }
}