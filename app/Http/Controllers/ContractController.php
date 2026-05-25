<?php
 
namespace App\Http\Controllers;
 
use App\Models\Contract;
use App\Models\Room;
use App\Models\Guest;
use App\Services\ContractService;
use Illuminate\Http\Request;
 
/**
 * ContractController - FIXED & COMPLETE
 * 
 * ✅ FIXES:
 * 1. Added advance_payment_months to validation
 * 2. Proper error handling
 * 3. Better N+1 query prevention
 */
class ContractController extends Controller
{
    public function __construct(protected readonly ContractService $contractService)
    {
    }
 
    public function index(Request $request)
    {
        $this->authorize('viewAny', Contract::class);
 
        // ✅ Eager load relations to prevent N+1
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
        $this->authorize('create', Contract::class);
 
        $rooms = Room::whereIn('status', ['available', 'occupied'])
            ->orderBy('room_number')
            ->get();
 
        $guests = Guest::all();
        $contractNumber = $this->contractService->generateContractNumber();
 
        return view('contracts.create', compact('rooms', 'guests', 'contractNumber'));
    }
 
    public function store(Request $request)
    {
        $this->authorize('create', Contract::class);
 
        $validated = $request->validate([
            'contract_number' => 'nullable|string|max:255|unique:contracts,contract_number',
            'room_id' => 'required|exists:rooms,id',
            'guest_id' => 'required|exists:guests,id',
            'title' => 'required|max:200',
            'contract_date' => 'nullable|date',
            'landlord_name' => 'nullable|string|max:255',
            'landlord_id_number' => 'nullable|string|max:20',
            'landlord_address' => 'nullable|max:500',
            'landlord_phone' => 'nullable|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'monthly_rent_text' => 'nullable|string|max:500',
            'deposit' => 'nullable|numeric|min:0',
            'advance_payment_months' => 'nullable|integer|min:0|max:60', // ✅ ADDED
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
 
        try {
            $this->contractService->create($validated);
 
            return redirect()->route('contracts.index')
                ->with('success', 'สัญญาเช่าถูกสร้างสำเร็จ');
        } catch (\Exception $e) {
            \Log::error('ContractController store failed', ['error' => $e->getMessage()]);
 
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'ไม่สามารถสร้างสัญญาได้ โปรดลองใหม่']);
        }
    }
 
    public function show(Contract $contract)
    {
        $this->authorize('view', $contract);
 
        $contract->load(['room', 'guest']);
 
        return view('contracts.show', compact('contract'));
    }
 
    public function edit(Contract $contract)
    {
        $this->authorize('update', $contract);
 
        $rooms = Room::all();
        $guests = Guest::all();
 
        return view('contracts.edit', compact('contract', 'rooms', 'guests'));
    }
 
    public function update(Request $request, Contract $contract)
    {
        $this->authorize('update', $contract);
 
        $validated = $request->validate([
            'contract_number' => 'nullable|string|max:255|unique:contracts,contract_number,' . $contract->id,
            'room_id' => 'required|exists:rooms,id',
            'guest_id' => 'required|exists:guests,id',
            'title' => 'required|max:200',
            'contract_date' => 'nullable|date',
            'landlord_name' => 'nullable|string|max:255',
            'landlord_id_number' => 'nullable|string|max:20',
            'landlord_address' => 'nullable|max:500',
            'landlord_phone' => 'nullable|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'monthly_rent_text' => 'nullable|string|max:500',
            'deposit' => 'nullable|numeric|min:0',
            'advance_payment_months' => 'nullable|integer|min:0|max:60', // ✅ ADDED
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
 
        try {
            $this->contractService->update($contract, $validated);
 
            return redirect()->route('contracts.show', $contract)
                ->with('success', 'สัญญาเช่าถูกแก้ไขสำเร็จ');
        } catch (\Exception $e) {
            \Log::error('ContractController update failed', ['error' => $e->getMessage()]);
 
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'ไม่สามารถแก้ไขสัญญาได้ โปรดลองใหม่']);
        }
    }
 
    public function destroy(Contract $contract)
    {
        $this->authorize('delete', $contract);
 
        try {
            $roomId = $contract->room_id;
            $contract->delete();
 
            if ($roomId) {
                $this->contractService->updateRoomStatus($roomId, 'available');
            }
 
            return redirect()->route('contracts.index')
                ->with('success', 'สัญญาเช่าถูกลบสำเร็จ');
        } catch (\Exception $e) {
            \Log::error('ContractController destroy failed', ['error' => $e->getMessage()]);
 
            return redirect()->back()
                ->withErrors(['error' => 'ไม่สามารถลบสัญญาได้ โปรดลองใหม่']);
        }
    }
 
    public function export(Request $request)
    {
        $this->authorize('viewAny', Contract::class);
 
        try {
            $contracts = Contract::with(['room', 'guest'])
                ->orderBy('id', 'desc')
                ->get();
 
            $filename = 'contracts_export_' . date('Y-m-d_H-i-s') . '.xlsx';
 
            $rows = $this->contractService->formatForExport($contracts);
 
            return xlsx_download($filename, $rows);
        } catch (\Exception $e) {
            \Log::error('ContractController export failed', ['error' => $e->getMessage()]);
 
            return redirect()->back()
                ->withErrors(['error' => 'ไม่สามารถ export ได้ โปรดลองใหม่']);
        }
    }
 
    public function expiring()
    {
        $this->authorize('viewAny', Contract::class);
 
        $contracts = Contract::with(['room', 'guest'])
            ->where('status', 'active')
            ->whereDate('end_date', '>=', now())
            ->whereDate('end_date', '<=', now()->addDays(30))
            ->orderBy('end_date', 'asc')
            ->paginate(10)
            ->withQueryString();
 
        return view('contracts.expiring', compact('contracts'));
    }
}
 