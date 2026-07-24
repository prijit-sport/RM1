<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\LoginResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResource|JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // ⚠️ Dummy hash to prevent timing attack:
        // Always run Hash::check() with a real bcrypt hash,
        // so response time is consistent whether the user exists or not.
        $dummyHash = '$2y$12$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWX';
        $passwordValid = Hash::check(
            $request->password,
            $user->password ?? $dummyHash
        );

        // Combined validation: user not found OR password mismatch
        if (!$user || !$passwordValid) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $isActive = $user->is_active ?? true;
        if (!$isActive) {
            return response()->json([
                'message' => 'Account is inactive'
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return new LoginResource([
            'user'  => $user->load('role'),
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function me(Request $request): JsonResource
    {
        return new UserResource($request->user()->load('role'));
    }
}