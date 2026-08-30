<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:100'],
        ]);

        // Même limitation de tentatives que la connexion web (§4/§16).
        $throttleKey = 'api-login:'.$request->ip().'|'.$credentials['email'];

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Trop de tentatives. Réessayez plus tard.',
            ]);
        }

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Auth::validate(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'Identifiants invalides.',
            ]);
        }

        if (! $user->isActive()) {
            throw ValidationException::withMessages([
                'email' => 'Ce compte est suspendu.',
            ]);
        }

        \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
        $user->update(['last_login_at' => now()]);

        $abilities = $user->isCompanyUser() ? ['search', 'verify'] : ['*'];
        $token = $user->createToken($credentials['device_name'], $abilities);

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'roles' => $user->getRoleNames(),
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté.']);
    }
}
