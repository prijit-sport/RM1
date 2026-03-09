<?php

namespace App\Http\Controllers\Api;

use App\Models\Contract;
use App\Services\ContractService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function __construct(private readonly ContractService $contractService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $query = Contract::with(['room', 'guest']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('contract_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('guest', function ($q) use ($request) {
                      $q->where('first_name', 'like', '%' . $request->search . '%')
                        ->orWhere('last_name', 'like', '%' . $request->search . '%');
                  })->orWhereHas('room', function ($q) use ($request) {
                      $q->where('room_number', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $contracts = $query->latest()->paginate(10);

        return response()->json($contracts);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_id' => 'required|exists:guests,id',
            'title' => 'required|max:200',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,pending,active,completed,cancelled',
            'description' => 'nullable|max:1000',
            'notes' => 'nullable|max:1000',
        ]);

        $contract = $this->contractService->create($validated);

        return response()->json($contract, 201);
    }

    public function show(Contract $contract): JsonResponse
    {
        return response()->json($contract->load(['room', 'guest']));
    }

    public function update(Request $request, Contract $contract): JsonResponse
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_id' => 'required|exists:guests,id',
            'title' => 'required|max:200',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,pending,active,completed,cancelled',
            'description' => 'nullable|max:1000',
            'notes' => 'nullable|max:1000',
        ]);

        $contract = $this->contractService->update($contract, $validated);

        return response()->json($contract);
    }

    public function destroy(Contract $contract): JsonResponse
    {
        $contract->delete();

        return response()->json(['message' => 'Contract deleted successfully']);
    }
}

