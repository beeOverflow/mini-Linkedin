<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!auth()->check() || auth()->user()->role !== $role) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        return $next($request);
    }
}