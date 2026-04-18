<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Offre;

class AdminController extends Controller
{
    // GET /api/admin/users
    public function listUsers()
    {
        $users = User::with('profil')->get();

        return response()->json($users);
    }

    // DELETE /api/admin/users/{user}
    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }

        // Empêcher l'admin de se supprimer lui-même
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }

    // PATCH /api/admin/offres/{offre}
    public function toggleOffre($id)
    {
        $offre = Offre::find($id);

        if (!$offre) {
            return response()->json(['message' => 'Offre introuvable'], 404);
        }

        $offre->update(['actif' => !$offre->actif]);

        $statut = $offre->actif ? 'activée' : 'désactivée';

        return response()->json([
            'message' => "Offre {$statut} avec succès",
            'offre'   => $offre,
        ]);
    }
}