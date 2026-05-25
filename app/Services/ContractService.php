<?php
 
namespace App\Services;
 
use App\Models\Contract;
use App\Models\Room;
use App\Models\Guest;
use App\Support\AuditLogger;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
 
/**
 * ═══════════════════════════════════════════════════════════════════════════
 * ContractService - FINAL COMPLETE VERSION
 * 
 * ✅ FIXES APPLIED:
 * 1. ✓ advance_payment คำนวณจาก advance_payment_months × monthly_rent
 * 2. ✓ deposit auto default เป็น monthly_rent ถ้าว่าง
 * 3. ✓ Allow override deposit manually
 * 4. ✓ Remove "force" code ที่ไม่ยืดหยุ่น
 * 5. ✓ ทำให้ advance_payment_months ไม่ถูก override
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
class ContractService
{
    private const STATUSES = ['draft', 'pending', 'active', 'completed', 'cancelled'];
 
    /**
     * Create a new contract
     * 
     * LOGIC:
     * 1. Generate contract number (auto)
     * 2. Validate dates (end > start)
     * 3. Calculate advance_payment = monthly_rent × advance_payment_months
     * 4. Default deposit = monthly_rent (if empty)
     * 5. Save to database
     * 6. Update room status (if active)
     */
    public function create(array $validated): Contract
    {
        \Log::info('🔵 ContractService::create() START', [
            'monthly_rent' => $validated['monthly_rent'] ?? null,
            'deposit_input' => $validated['deposit'] ?? null,
            'advance_months' => $validated['advance_payment_months'] ?? null,
        ]);
 
        // ─── Step 1: Auto-generate contract number ───────────────────────
        if (empty($validated['contract_number'] ?? null)) {
            $validated['contract_number'] = $this->generateContractNumber();
            \Log::info('📝 Generated contract number', ['number' => $validated['contract_number']]);
        }
 
        // ─── Step 2: Validate dates ───────────────────────────────────────
        if (isset($validated['start_date']) && isset($validated['end_date'])) {
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
 
            if ($endDate->isBefore($startDate)) {
                \Log::warning('❌ Invalid date range', [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                ]);
                throw ValidationException::withMessages([
                    'end_date' => 'วันสิ้นสุดต้องหลังจากวันเริ่มต้น',
                ]);
            }
        }
 
        // ─── Step 3: Calculate advance_payment ───────────────────────────
        // ✅ KEY FIX: Calculate from months × monthly_rent
        if (isset($validated['monthly_rent']) && isset($validated['advance_payment_months'])) {
            $monthly_rent = (float)$validated['monthly_rent'];
            $months = (int)($validated['advance_payment_months'] ?? 1);
            $calculated_advance = $monthly_rent * max(1, $months);
            
            $validated['advance_payment'] = $calculated_advance;
            
            \Log::info('💰 Calculated advance_payment', [
                'monthly_rent' => $monthly_rent,
                'months' => $months,
                'result' => $calculated_advance,
                'formula' => "{$monthly_rent} × {$months} = {$calculated_advance}",
            ]);
        } else {
            // If no data, set to 0
            $validated['advance_payment'] = 0;
        }
 
        // ─── Step 4: Default deposit to monthly_rent ──────────────────────
        // ✅ KEY FIX: If deposit is empty, auto = monthly_rent
        $original_deposit = $validated['deposit'] ?? null;
        
        if (!isset($validated['deposit']) || $validated['deposit'] === null || $validated['deposit'] === '') {
            $validated['deposit'] = (float)($validated['monthly_rent'] ?? 0);
            \Log::info('🔧 Deposit auto-filled', [
                'input' => $original_deposit,
                'default_to' => $validated['deposit'],
                'reason' => 'empty or null',
            ]);
        } else {
            $validated['deposit'] = (float)$validated['deposit'];
            \Log::info('🔧 Deposit manual override', [
                'value' => $validated['deposit'],
            ]);
        }
 
        // ─── Step 5: Ensure advance_payment_months is set ──────────────────
        if (!isset($validated['advance_payment_months'])) {
            $validated['advance_payment_months'] = 1;
        } else {
            $validated['advance_payment_months'] = (int)$validated['advance_payment_months'];
        }
 
        // ─── Step 6: Check room status ────────────────────────────────────
        $shouldOccupied = isset($validated['status']) && $validated['status'] === 'active';
 
        // ─── Step 7: Save to database (transaction) ──────────────────────
        $contract = DB::transaction(function () use ($validated, $shouldOccupied) {
            $contract = Contract::create($validated);
            \Log::info('✅ Contract created', [
                'id' => $contract->id,
                'number' => $contract->contract_number,
            ]);
 
            // Update room status if active
            if ($shouldOccupied && $contract->room_id) {
                $contract->room->update(['status' => 'occupied']);
                \Log::info('🏠 Room status updated', [
                    'room_id' => $contract->room_id,
                    'status' => 'occupied',
                ]);
            }
 
            // Audit log
            AuditLogger::log('contract.created', $contract);
 
            return $contract;
        });
 
        \Log::info('🟢 ContractService::create() SUCCESS', [
            'id' => $contract->id,
            'monthly_rent' => $contract->monthly_rent,
            'deposit' => $contract->deposit,
            'advance_payment' => $contract->advance_payment,
        ]);
 
        return $contract;
    }
 
    /**
     * Update an existing contract
     * 
     * SAME LOGIC AS CREATE
     * But also handles room status transitions
     */
    public function update(Contract $contract, array $validated): Contract
    {
        \Log::info('🔵 ContractService::update() START', [
            'contract_id' => $contract->id,
            'old_status' => $contract->status,
            'new_status' => $validated['status'] ?? null,
        ]);
 
        $oldStatus = $contract->status;
        $oldRoomId = $contract->room_id;
 
        // ─── Validate dates ──────────────────────────────────────────────
        if (isset($validated['start_date']) && isset($validated['end_date'])) {
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
 
            if ($endDate->isBefore($startDate)) {
                throw ValidationException::withMessages([
                    'end_date' => 'วันสิ้นสุดต้องหลังจากวันเริ่มต้น',
                ]);
            }
        }
 
        // ─── Calculate advance_payment ───────────────────────────────────
        if (isset($validated['monthly_rent']) && isset($validated['advance_payment_months'])) {
            $monthly_rent = (float)$validated['monthly_rent'];
            $months = (int)($validated['advance_payment_months'] ?? 1);
            $calculated_advance = $monthly_rent * max(1, $months);
            
            $validated['advance_payment'] = $calculated_advance;
            
            \Log::info('💰 Recalculated advance_payment', [
                'monthly_rent' => $monthly_rent,
                'months' => $months,
                'result' => $calculated_advance,
            ]);
        }
 
        // ─── Default deposit ─────────────────────────────────────────────
        if (!isset($validated['deposit']) || $validated['deposit'] === null || $validated['deposit'] === '') {
            $validated['deposit'] = (float)($validated['monthly_rent'] ?? 0);
        } else {
            $validated['deposit'] = (float)$validated['deposit'];
        }
 
        // ─── Ensure advance_payment_months ──────────────────────────────
        if (!isset($validated['advance_payment_months'])) {
            $validated['advance_payment_months'] = 1;
        } else {
            $validated['advance_payment_months'] = (int)$validated['advance_payment_months'];
        }
 
        $newStatus = $validated['status'] ?? $oldStatus;
        $newRoomId = $validated['room_id'] ?? $oldRoomId;
 
        // ─── Save to database (transaction) ──────────────────────────────
        $contract = DB::transaction(function () use ($contract, $validated, $oldStatus, $newStatus, $oldRoomId, $newRoomId) {
            $contract->update($validated);
            \Log::info('✅ Contract updated', [
                'id' => $contract->id,
                'new_status' => $newStatus,
            ]);
 
            // Handle room status changes
            if ($oldStatus === 'active' && $newStatus !== 'active' && $oldRoomId) {
                $this->updateRoomStatus($oldRoomId, 'available');
                \Log::info('🏠 Room freed', ['room_id' => $oldRoomId]);
            }
 
            if ($newStatus === 'active' && $newRoomId) {
                $this->updateRoomStatus($newRoomId, 'occupied');
                \Log::info('🏠 Room occupied', ['room_id' => $newRoomId]);
            }
 
            AuditLogger::log('contract.updated', $contract);
 
            return $contract->fresh();
        });
 
        \Log::info('🟢 ContractService::update() SUCCESS', [
            'id' => $contract->id,
            'advance_payment' => $contract->advance_payment,
            'deposit' => $contract->deposit,
        ]);
 
        return $contract;
    }
 
    /**
     * Activate a contract
     */
    public function activate(Contract $contract): Contract
    {
        if ($contract->status !== 'pending' && $contract->status !== 'draft') {
            throw ValidationException::withMessages([
                'status' => 'เฉพาะสัญญาร่างหรือรออนุมัติเท่านั้นที่สามารถเปิดใช้งานได้',
            ]);
        }
 
        $contract = DB::transaction(function () use ($contract) {
            $contract->update(['status' => 'active']);
 
            if ($contract->room_id) {
                $this->updateRoomStatus($contract->room_id, 'occupied');
            }
 
            AuditLogger::log('contract.activated', $contract);
 
            return $contract->fresh();
        });
 
        return $contract;
    }
 
    /**
     * Cancel a contract
     */
    public function cancel(Contract $contract): Contract
    {
        if ($contract->status === 'cancelled' || $contract->status === 'completed') {
            throw ValidationException::withMessages([
                'status' => 'ไม่สามารถยกเลิกสัญญานี้ได้',
            ]);
        }
 
        $contract = DB::transaction(function () use ($contract) {
            $contract->update(['status' => 'cancelled']);
 
            if ($contract->room_id) {
                $this->updateRoomStatus($contract->room_id, 'available');
            }
 
            AuditLogger::log('contract.cancelled', $contract);
 
            return $contract->fresh();
        });
 
        return $contract;
    }
 
    /**
     * Complete a contract
     */
    public function complete(Contract $contract): Contract
    {
        if ($contract->status !== 'active') {
            throw ValidationException::withMessages([
                'status' => 'เฉพาะสัญญาที่ใช้งานเท่านั้นที่สามารถจบได้',
            ]);
        }
 
        $contract = DB::transaction(function () use ($contract) {
            $contract->update(['status' => 'completed']);
 
            if ($contract->room_id) {
                $this->updateRoomStatus($contract->room_id, 'available');
            }
 
            AuditLogger::log('contract.completed', $contract);
 
            return $contract->fresh();
        });
 
        return $contract;
    }
 
    /**
     * Renew a contract
     */
    public function renew(Contract $contract, array $newData): Contract
    {
        if ($contract->status !== 'completed' && $contract->status !== 'cancelled') {
            if ($contract->status === 'active') {
                $this->complete($contract);
            }
        }
 
        $newData['room_id'] = $newData['room_id'] ?? $contract->room_id;
        $newData['guest_id'] = $newData['guest_id'] ?? $contract->guest_id;
        $newData['contract_number'] = $this->generateContractNumber();
 
        return $this->create($newData);
    }
 
    /**
     * Get expiring contracts (within 30 days)
     */
    public function getExpiringContracts(int $days = 30): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Contract::where('status', 'active')
            ->whereDate('end_date', '>=', Carbon::today())
            ->whereDate('end_date', '<=', Carbon::today()->addDays($days))
            ->with(['room', 'guest'])
            ->orderBy('end_date', 'asc')
            ->paginate(10);
    }
 
    /**
     * Get active contract for a specific room
     */
    public function getActiveContractForRoom(int $roomId): ?Contract
    {
        return Contract::where('room_id', $roomId)
            ->where('status', 'active')
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->first();
    }
 
    /**
     * Generate unique contract number
     * Format: CNT0001, CNT0002, ...
     */
    public function generateContractNumber(): string
    {
        $prefix = 'CNT';
        $year = now()->format('Y');
 
        $lastContract = Contract::whereYear('created_at', now()->year)
            ->orderByDesc('id')
            ->first();
 
        $sequence = $lastContract
            ? (intval(substr($lastContract->contract_number, -4)) + 1)
            : 1;
 
        return sprintf('%s%04d', $prefix, $sequence);
    }
 
    /**
     * Update room status
     * (Don't change if room is in maintenance)
     */
    public function updateRoomStatus(int $roomId, string $status): void
    {
        $room = Room::find($roomId);
        if ($room && $room->status !== 'maintenance') {
            $room->update(['status' => $status]);
        }
    }
 
    /**
     * Calculate contract duration in months
     */
    public function calculateDuration(Contract $contract): int
    {
        $start = Carbon::parse($contract->start_date);
        $end = Carbon::parse($contract->end_date);
 
        return $start->diffInMonths($end);
    }
 
    /**
     * Format contracts for Excel export
     */
    public function formatForExport(Collection $contracts): array
    {
        $rows = [
            [
                'เลขที่สัญญา',
                'ห้องพัก',
                'ชื่อผู้เช่า',
                'วันที่เริ่มต้น',
                'วันที่สิ้นสุด',
                'ค่าเช่า/เดือน',
                'เงินประกัน',
                'สถานะ',
            ],
        ];
 
        foreach ($contracts as $contract) {
            $rows[] = [
                $contract->contract_number ?? '-',
                $contract->room?->room_number ?? '-',
                trim(
                    ($contract->guest?->first_name ?? '') . ' ' .
                    ($contract->guest?->last_name ?? '')
                ) ?: '-',
                optional($contract->start_date)->format('d/m/Y') ?? '-',
                optional($contract->end_date)->format('d/m/Y') ?? '-',
                number_format((float)($contract->monthly_rent ?? 0), 2),
                number_format((float)($contract->deposit ?? 0), 2),
                $contract->status ?? '-',
            ];
        }
 
        return $rows;
    }
}
 