<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Role hierarchy: candidat (1) < recruteur (2) < admin (3)
     */
    protected $roles = [
        'candidat' => 1,
        'recruteur' => 2,
        'admin' => 3,
    ];

    public function handle(Request $request, Closure $next, string $role)
    {
        // 1. Ensure user is authenticated
        if (!auth()->check()) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $user = auth()->user();

        // 2. Resolve levels
        $userLevel = $this->roles[$user->role] ?? 0;
        $requiredLevel = $this->roles[$role] ?? 0;

        // 3. Compare levels
        if ($userLevel < $requiredLevel) {
            return response()->json([
                'message' => 'Accès refusé.',
                'required' => $role,
                'current' => $user->role
            ], 403);
        }

        return $next($request);
    }
}