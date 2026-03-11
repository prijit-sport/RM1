<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Room;
use App\Models\Guest;
use App\Support\AuditLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ContractService
{
    private const STATUSES = ['draft', 'pending', 'active', 'completed', 'cancelled'];

    /**
     * Create a new contract.
     */
    public function create(array $validated): Contract
    {
        // Auto-generate contract number if not provided
        if (empty($validated['contract_number'] ?? null)) {
            $validated['contract_number'] = $this->generateContractNumber();
        }

        // Validate dates
        if (isset($validated['start_date']) && isset($validated['end_date'])) {
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
            
            if ($endDate->isBefore($startDate)) {
                throw ValidationException::withMessages([
                    'end_date' => 'End date must be after start date.',
                ]);
            }
        }

        // Update room status to occupied if contract is active
        $shouldOccupied = isset($validated['status']) && $validated['status'] === 'active';

        $contract = DB::transaction(function () use ($validated, $shouldOccupied) {
            $contract = Contract::create($validated);
            
            if ($shouldOccupied && $contract->room_id) {
                $contract->room->update(['status' => 'occupied']);
            }

            AuditLogger::log('contract.created', $contract);
            
            return $contract;
        });

        return $contract;
    }

    /**
     * Update an existing contract.
     */
    public function update(Contract $contract, array $validated): Contract
    {
        $oldStatus = $contract->status;
        $oldRoomId = $contract->room_id;

        // Validate dates
        if (isset($validated['start_date']) && isset($validated['end_date'])) {
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
            
            if ($endDate->isBefore($startDate)) {
                throw ValidationException::withMessages([
                    'end_date' => 'End date must be after start date.',
                ]);
            }
        }

        $newStatus = $validated['status'] ?? $oldStatus;
        $newRoomId = $validated['room_id'] ?? $oldRoomId;

        $contract = DB::transaction(function () use ($contract, $validated, $oldStatus, $newStatus, $oldRoomId, $newRoomId) {
            $contract->update($validated);

            // Handle room status changes
            if ($oldStatus === 'active' && $newStatus !== 'active' && $oldRoomId) {
                $this->updateRoomStatus($oldRoomId, 'available');
            }

            if ($newStatus === 'active' && $newRoomId) {
                $this->updateRoomStatus($newRoomId, 'occupied');
            }

            AuditLogger::log('contract.updated', $contract);
            
            return $contract->fresh();
        });

        return $contract;
    }

    /**
     * Activate a contract.
     */
    public function activate(Contract $contract): Contract
    {
        if ($contract->status !== 'pending' && $contract->status !== 'draft') {
            throw ValidationException::withMessages([
                'status' => 'Only pending or draft contracts can be activated.',
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
     * Cancel a contract.
     */
    public function cancel(Contract $contract): Contract
    {
        if ($contract->status === 'cancelled' || $contract->status === 'completed') {
            throw ValidationException::withMessages([
                'status' => 'This contract cannot be cancelled.',
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
     * Complete a contract.
     */
    public function complete(Contract $contract): Contract
    {
        if ($contract->status !== 'active') {
            throw ValidationException::withMessages([
                'status' => 'Only active contracts can be completed.',
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
     * Renew a contract.
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
     * Get expiring contracts.
     */
    public function getExpiringContracts(int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return Contract::where('status', 'active')
            ->whereDate('end_date', '>=', Carbon::today())
            ->whereDate('end_date', '<=', Carbon::today()->addDays($days))
            ->with(['room', 'guest'])
            ->orderBy('end_date', 'asc')
            ->get();
    }

    /**
     * Get active contract for a room.
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
     * Generate unique contract number.
     */
    public function generateContractNumber(): string
    {
        $prefix = 'CNT';
        $year = now()->format('Y');
        
        $lastContract = Contract::whereYear('created_at', now()->year)
            ->orderByDesc('id')
            ->first();

        $sequence = $lastContract 
            ? (intval(substr($lastContract->contract_number, -5)) + 1)
            : 1;

        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    /**
     * Update room status.
     * Made public to allow access from Controller.
     */
    public function updateRoomStatus(int $roomId, string $status): void
    {
        $room = Room::find($roomId);
        if ($room && $room->status !== 'maintenance') {
            $room->update(['status' => $status]);
        }
    }

    /**
     * Calculate contract duration in months.
     */
    public function calculateDuration(Contract $contract): int
    {
        $start = Carbon::parse($contract->start_date);
        $end = Carbon::parse($contract->end_date);
        
        return $start->diffInMonths($end);
    }
}
