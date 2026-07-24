<?php
 
namespace App\Http\Controllers;
 
use App\Models\Contract;
use App\Models\Room;
use App\Models\Guest;
use App\Services\ContractService;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
 
class ContractController extends Controller
{
    public function __construct(protected readonly ContractService $contractService)
    {
    }
 
    public function index(Request $request)
    {
        $this->authorize('viewAny', Contract::class);
 
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
            ->paginate(config('rm1.items_per_page'))
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
 
    public function store(StoreContractRequest $request)
    {
        $this->authorize('create', Contract::class);
 
        $validated = $request->validated();
 
        // ✅ ตรวจสอบ end_date > start_date
        if (!empty($validated['start_date']) && !empty($validated['end_date'])) {
            $startDate = Carbon::parse($validated['start_date']);
            $endDate   = Carbon::parse($validated['end_date']);
            if (!$endDate->isAfter($startDate)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['end_date' => 'วันสิ้นสุดสัญญาต้องมากกว่าวันเริ่มสัญญา']);
            }
        }
 
        // ✅ ตรวจสอบ room occupied
        $room = Room::find($validated['room_id'] ?? null);
        if ($room && $room->status === 'occupied') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['room_id' => 'ห้องนี้มีผู้เช่าอยู่แล้ว ไม่สามารถสร้างสัญญาได้']);
        }
 
        try {
            $this->contractService->create($validated);
 
            return redirect()->route('contracts.index')
                ->with('success', 'สัญญาเช่าถูกสร้างสำเร็จ');
 
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
 
        } catch (\Exception $e) {
            \Log::error('ContractController store failed', ['error' => $e->getMessage()]);
 
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'ไม่สามารถสร้างสัญญาได้ กรุณาลองใหม่']);
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
 
    public function update(UpdateContractRequest $request, Contract $contract)
    {
        $this->authorize('update', $contract);
 
        try {
            $this->contractService->update($contract, $request->validated());
 
            return redirect()->route('contracts.show', $contract)
                ->with('success', 'สัญญาเช่าถูกอัปเดตสำเร็จ');
 
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
 
        } catch (\Exception $e) {
            \Log::error('ContractController update failed', ['error' => $e->getMessage()]);
 
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'ไม่สามารถอัปเดตสัญญาได้ กรุณาลองใหม่']);
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
                ->withErrors(['error' => 'ไม่สามารถลบสัญญาได้ กรุณาลองใหม่']);
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
                ->withErrors(['error' => 'ไม่สามารถ export ได้ กรุณาลองใหม่']);
        }
    }
 
    public function expiring()
    {
        $this->authorize('viewAny', Contract::class);
 
        $contracts = Contract::with(['room', 'guest'])
            ->where('status', 'active')
            ->whereDate('end_date', '>=', now())
            ->whereDate('end_date', '<=', now()->addDays(config('rm1.contract_expiry_warning_days')))
            ->orderBy('end_date', 'asc')
            ->paginate(config('rm1.items_per_page'))
            ->withQueryString();
 
        return view('contracts.expiring', compact('contracts'));
    }
}
 