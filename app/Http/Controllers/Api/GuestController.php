<?php

namespace App\Http\Controllers\Api;

use App\Models\Guest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Guest::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('id_number', 'like', '%' . $request->search . '%');
            });
        }

        $guests = $query->paginate(10);

        return response()->json($guests);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|email|unique:guests',
            'phone' => 'required|max:20',
            'address' => 'nullable|max:500',
            'city' => 'nullable|max:100',
            'country' => 'nullable|max:100',
            'id_number' => 'required|max:50|unique:guests',
        ]);

        $guest = Guest::create($validated);

        return response()->json($guest, 201);
    }

    public function show(Guest $guest): JsonResponse
    {
        return response()->json($guest->load(['bookings', 'contracts']));
    }

    public function update(Request $request, Guest $guest): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|email|unique:guests,email,' . $guest->id,
            'phone' => 'required|max:20',
            'address' => 'nullable|max:500',
            'city' => 'nullable|max:100',
            'country' => 'nullable|max:100',
            'id_number' => 'required|max:50|unique:guests,id_number,' . $guest->id,
        ]);

        $guest->update($validated);

        return response()->json($guest);
    }

    public function destroy(Guest $guest): JsonResponse
    {
        $guest->delete();

        return response()->json(['message' => 'Guest deleted successfully']);
    }
}

