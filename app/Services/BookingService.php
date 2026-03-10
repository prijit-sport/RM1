<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use App\Support\AuditLogger;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    private const ACTIVE_BOOKING_STATUSES = ['pending', 'confirmed', 'checked_in'];
    private const OCCUPY_ROOM_STATUSES = ['confirmed', 'checked_in'];
    private const ALLOWED_TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['checked_in', 'cancelled'],
        'checked_in' => ['checked_out'],
        'checked_out' => [],
        'cancelled' => [],
    ];

    public function create(array $validated): void
    {
        if (in_array($validated['status'], ['checked_in', 'checked_out'], true)) {
            throw ValidationException::withMessages([
                'status' => __('ui.booking.invalid_initial_status'),
            ]);
        }

        DB::transaction(function () use ($validated) {
            $room = $this->lockRoom((int) $validated['room_id']);
            $validated['total_price'] = $this->calculateTotal($room->price_per_month, $validated['check_in_date'], $validated['check_out_date']);

            if ($this->hasOverlappingBooking(
                (int) $validated['room_id'],
                (string) $validated['check_in_date'],
                (string) $validated['check_out_date'],
                null,
                true
            )) {
                throw ValidationException::withMessages([
                    'room_id' => __('ui.booking.overlap'),
                ]);
            }

            $booking = Booking::create($validated);
            $this->syncRoomStatus((int) $booking->room_id, true);
            AuditLogger::log('booking.created', $booking, [
                'status' => $booking->status,
            ]);
        });
    }

    public function update(Booking $booking, array $validated): void
    {
        DB::transaction(function () use ($booking, $validated) {
            $lockedBooking = Booking::query()->lockForUpdate()->findOrFail($booking->id);
            $oldRoomId = (int) $lockedBooking->room_id;
            $newRoom = $this->lockRoom((int) $validated['room_id']);

            if (! $this->canTransition((string) $lockedBooking->status, (string) $validated['status'])) {
                throw ValidationException::withMessages([
                    'status' => __('ui.booking.invalid_transition'),
                ]);
            }

            $validated['total_price'] = $this->calculateTotal($newRoom->price_per_month, $validated['check_in_date'], $validated['check_out_date']);

            if (
                in_array($validated['status'], self::ACTIVE_BOOKING_STATUSES, true)
                && $this->hasOverlappingBooking(
                    (int) $validated['room_id'],
                    (string) $validated['check_in_date'],
                    (string) $validated['check_out_date'],
                    (int) $lockedBooking->id,
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'room_id' => __('ui.booking.overlap'),
                ]);
            }

            $lockedBooking->update($validated);
            $this->syncRoomStatus((int) $lockedBooking->room_id, true);

            if ($oldRoomId !== (int) $lockedBooking->room_id) {
                $this->syncRoomStatus($oldRoomId, true);
            }

            AuditLogger::log('booking.updated', $lockedBooking, [
                'status' => $lockedBooking->status,
            ]);
        });
    }

    public function destroy(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $lockedBooking = Booking::query()->lockForUpdate()->findOrFail($booking->id);
            $roomId = (int) $lockedBooking->room_id;
            $this->lockRoom($roomId);
            $lockedBooking->delete();
            $this->syncRoomStatus($roomId, true);
            AuditLogger::log('booking.deleted', $booking);
        });
    }

    public function confirm(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $lockedBooking = Booking::query()->lockForUpdate()->findOrFail($booking->id);
            $this->lockRoom((int) $lockedBooking->room_id);

            if (! $this->canTransition((string) $lockedBooking->status, 'confirmed')) {
                throw ValidationException::withMessages([
                    'status' => __('ui.booking.confirm_only_pending'),
                ]);
            }

            $lockedBooking->update(['status' => 'confirmed']);
            $this->syncRoomStatus((int) $lockedBooking->room_id, true);
            AuditLogger::log('booking.confirmed', $lockedBooking);
        });
    }

    public function cancel(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $lockedBooking = Booking::query()->lockForUpdate()->findOrFail($booking->id);
            $this->lockRoom((int) $lockedBooking->room_id);

            if (! $this->canTransition((string) $lockedBooking->status, 'cancelled')) {
                throw ValidationException::withMessages([
                    'status' => __('ui.booking.cancel_not_allowed'),
                ]);
            }

            $lockedBooking->update(['status' => 'cancelled']);
            $this->syncRoomStatus((int) $lockedBooking->room_id, true);
            AuditLogger::log('booking.cancelled', $lockedBooking);
        });
    }

    private function calculateTotal(float $pricePerMonth, string $checkInDate, string $checkOutDate): float
    {
        $checkIn = new DateTime($checkInDate);
        $checkOut = new DateTime($checkOutDate);
        $days = $checkOut->diff($checkIn)->days;
        
        // Handle zero or negative price
        if ($pricePerMonth <= 0) {
            return 0;
        }
        
        // Calculate daily rate from monthly price
        // Using actual days in month (average 30.44 days per month)
        $dailyRate = $pricePerMonth / 30.44;
        
        // Calculate total based on actual days
        return round($dailyRate * $days, 2);
    }

    private function hasOverlappingBooking(
        int $roomId,
        string $checkInDate,
        string $checkOutDate,
        ?int $ignoreBookingId = null,
        bool $forUpdate = false
    ): bool {
        $query = Booking::query()
            ->where('room_id', $roomId)
            ->when($ignoreBookingId, function (Builder $query) use ($ignoreBookingId) {
                $query->where('id', '!=', $ignoreBookingId);
            })
            ->whereIn('status', self::ACTIVE_BOOKING_STATUSES)
            ->where(function (Builder $query) use ($checkInDate, $checkOutDate) {
                $query->where('check_in_date', '<', $checkOutDate)
                    ->where('check_out_date', '>', $checkInDate);
            });

        if ($forUpdate) {
            $query->lockForUpdate();
        }

        return $query->exists();
    }

    private function syncRoomStatus(int $roomId, bool $lockRoom = false): void
    {
        $roomQuery = Room::query();
        if ($lockRoom) {
            $roomQuery->lockForUpdate();
        }

        $room = $roomQuery->find($roomId);
        if (! $room || $room->status === 'maintenance') {
            return;
        }

        $bookingQuery = Booking::query()
            ->where('room_id', $roomId)
            ->whereIn('status', self::OCCUPY_ROOM_STATUSES);

        if ($lockRoom) {
            $bookingQuery->lockForUpdate();
        }

        $targetStatus = $bookingQuery->exists() ? 'occupied' : 'available';
        if ($room->status !== $targetStatus) {
            $room->update(['status' => $targetStatus]);
        }
    }

    private function lockRoom(int $roomId): Room
    {
        return Room::query()->lockForUpdate()->findOrFail($roomId);
    }

    private function canTransition(string $from, string $to): bool
    {
        if ($from === $to) {
            return true;
        }

        return in_array($to, self::ALLOWED_TRANSITIONS[$from] ?? [], true);
    }
}
