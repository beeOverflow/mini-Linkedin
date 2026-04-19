<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTGuard;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:candidat,recruteur,admin'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user
        ], 201);
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        /** @var JWTGuard $guard */
        $guard = Auth::guard('api');

        if (!$token = $guard->attempt($credentials)) {
            return response()->json([
                'message' => 'Email ou mot de passe incorrect',
            ], 401);
        }
        return $this->respondWithToken($token);
    }


    public function logout()
    {
        /** @var JWTGuard $guard */
        $guard = Auth::guard('api');
        $guard->logout();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ], 200);
    }

    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    public function refresh()
    {
        /** @var JWTGuard $guard */
        $guard = Auth::guard('api');
        return $this->respondWithToken($guard->refresh());
    }

    protected function respondWithToken($token)
    {
        /** @var JWTGuard $guard */
        $guard = Auth::guard('api');

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $guard->factory()->getTTL() * 60
        ]);
    }
}