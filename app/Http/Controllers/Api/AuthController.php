<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
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

        return response()->json([
            'user' => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'is_active'  => $user->is_active,
                'created_at' => $user->created_at,
                'role'       => $user->role ? [
                    'name'  => $user->role->name,
                    'label' => $user->role->label ?? $user->role->name,
                ] : null,
            ],
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

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('role');

        return response()->json([
            'user' => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'is_active'  => $user->is_active,
                'created_at' => $user->created_at,
                'role'       => $user->role ? [
                    'name'  => $user->role->name,
                    'label' => $user->role->label ?? $user->role->name,
                ] : null,
            ],
        ]);
    }
}