<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Room;
use App\Models\Guest;
use App\Services\ContractService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContractController extends Controller
{
    public function __construct(private readonly ContractService $contractService)
    {
        Log::info('[ContractController] Constructor called');
    }

    public function index(Request $request)
    {
        Log::info('[ContractController] index - User authenticated: ' . (auth()->check() ? 'Yes - ' . auth()->user()->name : 'No'));
        Log::info('[ContractController] index - Session ID: ' . $request->session()->getId());
        
        $contracts = Contract::with(['room', 'guest'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('contract_number', 'like', "%{$search}%")
                        ->orWhereHas('guest', function ($guestQuery) use ($search) {
                            $guestQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('room', function ($roomQuery) use ($search) {
                            $roomQuery->where('room_number', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('contracts.index', compact('contracts'));
    }

    public function create()
    {
        $rooms = Room::where('status', 'available')->get();
        $guests = Guest::all();
        
        return view('contracts.create', compact('rooms', 'guests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_id' => 'required|exists:guests,id',
            'title' => 'required|max:200',
            'contract_date' => 'nullable|date',
            'landlord_name' => 'nullable|string|max:255',
            'landlord_address' => 'nullable|max:500',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'electricity_rate' => 'nullable|numeric|min:0',
            'water_rate' => 'nullable|numeric|min:0',
            'late_fee' => 'nullable|numeric|min:0',
            'other_fees' => 'nullable|max:1000',
            'status' => 'required|in:draft,pending,active,completed,cancelled',
            'terms' => 'nullable|max:5000',
            'tenant_signature' => 'nullable|string|max:255',
            'landlord_signature' => 'nullable|string|max:255',
            'witness_signature' => 'nullable|string|max:255',
            'tenant_sign_date' => 'nullable|date',
            'landlord_sign_date' => 'nullable|date',
            'witness_sign_date' => 'nullable|date',
            'description' => 'nullable|max:1000',
            'notes' => 'nullable|max:1000',
        ]);

        $this->contractService->create($validated);

        return redirect()->route('contracts.index')->with('success', __('ui.contract.created'));
    }

    public function show(Contract $contract)
    {
        $contract->load(['room', 'guest']);
        
        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $rooms = Room::all();
        $guests = Guest::all();
        
        return view('contracts.edit', compact('contract', 'rooms', 'guests'));
    }

    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_id' => 'required|exists:guests,id',
            'title' => 'required|max:200',
            'contract_date' => 'nullable|date',
            'landlord_name' => 'nullable|string|max:255',
            'landlord_address' => 'nullable|max:500',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'electricity_rate' => 'nullable|numeric|min:0',
            'water_rate' => 'nullable|numeric|min:0',
            'late_fee' => 'nullable|numeric|min:0',
            'other_fees' => 'nullable|max:1000',
            'status' => 'required|in:draft,pending,active,completed,cancelled',
            'terms' => 'nullable|max:5000',
            'tenant_signature' => 'nullable|string|max:255',
            'landlord_signature' => 'nullable|string|max:255',
            'witness_signature' => 'nullable|string|max:255',
            'tenant_sign_date' => 'nullable|date',
            'landlord_sign_date' => 'nullable|date',
            'witness_sign_date' => 'nullable|date',
            'description' => 'nullable|max:1000',
            'notes' => 'nullable|max:1000',
        ]);

        $this->contractService->update($contract, $validated);

        return redirect()->route('contracts.show', $contract)->with('success', __('ui.contract.updated'));
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()->route('contracts.index')->with('success', __('ui.contract.deleted'));
    }

    public function export(Request $request)
    {
        $contracts = Contract::with(['room', 'guest'])->orderBy('id', 'desc')->get();
        $filename = 'contracts_export_' . date('Y-m-d') . '.xlsx';

        $rows = [];
        $rows[] = [
            __('ui.contract.number'),
            __('ui.contract.room'),
            __('ui.contract.guest'),
            __('ui.contract.start_date'),
            __('ui.contract.end_date'),
            __('ui.contract.monthly_rent'),
            __('ui.contract.deposit'),
            __('ui.contract.status'),
        ];

        foreach ($contracts as $contract) {
            $rows[] = [
                $contract->contract_number,
                $contract->room->room_number ?? '-',
                trim(($contract->guest->first_name ?? '') . ' ' . ($contract->guest->last_name ?? '')) ?: '-',
                optional($contract->start_date)->format('d/m/Y'),
                optional($contract->end_date)->format('d/m/Y'),
                $contract->monthly_rent,
                $contract->deposit ?? 0,
                $contract->status,
            ];
        }

        return xlsx_download($filename, $rows);
    }

    public function generatePdf(Contract $contract)
    {
        return redirect()->route('contracts.show', $contract)->with('info', __('ui.common.pdf_coming_soon'));
    }

    public function expiring()
    {
        $contracts = $this->contractService->getExpiringContracts(30);
        
        return view('contracts.index', compact('contracts'));
    }
}

