<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

use App\Models\Booking;
use App\Models\Meter;
use App\Models\MeterReading;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    // ─────────────────────────────────────────
    //  CREATE
    // ─────────────────────────────────────────
    public function create(array $data): Booking
    {
        $data['rent_amount'] = $data['rent_amount'] ?? 0;

        $data['deposit_amount'] = $data['deposit_amount'] ?? 0;
        $data['total_price'] = (float) $data['rent_amount'] + (float) $data['deposit_amount'];

        $checkIn = $data['check_in_date'] ?? null;
        $checkOut = $data['check_out_date'] ?? null;

        if ($checkIn && $checkOut) {
            $overlap = Booking::where('room_id', $data['room_id'])
                ->whereIn('status', [
                    Booking::STATUS_PENDING,
                    Booking::STATUS_CONFIRMED,
                    Booking::STATUS_CANCELLED,
                ])
                ->where(function ($q) use ($checkIn, $checkOut) {
                    // overlap when: existing.check_in < new.check_out AND existing.check_out > new.check_in
                    // boundary case checkout == other checkin should NOT overlap
                    $q->where('check_in_date', '<', $checkOut)
                        ->where('check_out_date', '>', $checkIn);
                })
                ->exists();

            if ($overlap) {
                throw ValidationException::withMessages([
                    'room_id' => ['Overlapping booking'],
                ]);
            }
        }

        return DB::transaction(function () use ($data) {
            $booking = Booking::create($data);

            Cache::forget('layout_notifications');

            if ($booking->status === Booking::STATUS_CONFIRMED) {
                $this->lockRoom($booking->room_id);
            }


            return $booking;
        });
    }

    // ─────────────────────────────────────────
    //  UPDATE
    // ─────────────────────────────────────────
    public function update(Booking $booking, array $data): Booking
    {
        return DB::transaction(function () use ($booking, $data) {
            $oldRoomId = $booking->room_id;
            $oldStatus = $booking->status;
            $newRoomId = $data['room_id'] ?? $oldRoomId;
            $newStatus = $data['status'] ?? $oldStatus;

            $allowedTransitions = [
                Booking::STATUS_PENDING => [Booking::STATUS_CONFIRMED, Booking::STATUS_CANCELLED],
                Booking::STATUS_CONFIRMED => [Booking::STATUS_CANCELLED],
                Booking::STATUS_CANCELLED => [],
            ];

            $allowedNext = $allowedTransitions[$oldStatus] ?? [];
            if ($oldStatus !== $newStatus && !in_array($newStatus, $allowedNext, true)) {
                throw ValidationException::withMessages([
                    'status' => ['Invalid status transition'],
                ]);
            }

            if (
                $oldRoomId !== $newRoomId ||
                ($booking->check_in_date !== ($data['check_in_date'] ?? $booking->check_in_date)) ||
                ($booking->check_out_date !== ($data['check_out_date'] ?? $booking->check_out_date))
            ) {
                $checkIn = $data['check_in_date'] ?? $booking->check_in_date;
                $checkOut = $data['check_out_date'] ?? $booking->check_out_date;

                $overlap = Booking::where('room_id', $newRoomId)
                    ->where('id', '!=', $booking->id)
                    ->whereIn('status', [
                        Booking::STATUS_PENDING,
                        Booking::STATUS_CONFIRMED,
                        Booking::STATUS_CANCELLED,
                    ])
                    ->where(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in_date', '<', $checkOut)
                            ->where('check_out_date', '>', $checkIn);
                    })
                    ->exists();

                if ($overlap) {
                    throw ValidationException::withMessages([
                        'room_id' => ['Overlapping booking'],
                    ]);
                }
            }

            $booking->update($data);

            // Sync room status
            if ($oldRoomId !== $newRoomId) {
                $this->releaseRoom($oldRoomId);

                if ($newStatus === Booking::STATUS_CONFIRMED) {
                    $this->lockRoom($newRoomId);
                }

                return $booking;
            }

            if ($oldStatus !== $newStatus) {
                if ($newStatus === Booking::STATUS_CONFIRMED) {
                    $this->lockRoom($newRoomId);
                } elseif ($newStatus === Booking::STATUS_CANCELLED) {
                    $this->releaseRoom($newRoomId);
                }
            }

            return $booking;

        });
    }

    // ─────────────────────────────────────────
    //  CONFIRM + สร้าง Meter + MeterReading
    // ─────────────────────────────────────────
    public function confirm(Booking $booking, array $rates = []): Booking
    {
        if ($booking->status !== Booking::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'status' => ['สถานะต้องเป็น pending เท่านั้น'],
            ]);
        }

        DB::transaction(function () use ($booking, $rates) {
            $booking->update(['status' => Booking::STATUS_CONFIRMED]);
            $this->lockRoom($booking->room_id);

            $electricRate = (float) ($rates['electric_rate'] ?? 0);
            $waterRate = (float) ($rates['water_rate'] ?? 0);
            $taxRate = (float) ($rates['tax_rate'] ?? 0);

            $electricMeter = $this->ensureMeter($booking->room_id, 'electric', $electricRate, $taxRate);
            $waterMeter = $this->ensureMeter($booking->room_id, 'water', $waterRate, $taxRate);

            $checkInDate = $booking->check_in_date
                ? Carbon::parse($booking->check_in_date)->toDateString()
                : now()->toDateString();

            $this->createInitialReading(
                $electricMeter,
                $booking,
                (float) ($booking->electric_meter_start ?? 0),
                $checkInDate
            );

            $this->createInitialReading(
                $waterMeter,
                $booking,
                (float) ($booking->water_meter_start ?? 0),
                $checkInDate
            );
        });

        return $booking->fresh();
    }

    // ─────────────────────────────────────────
    //  CANCEL
    // ─────────────────────────────────────────
    public function cancel(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {
            $booking->update(['status' => Booking::STATUS_CANCELLED]);
            $this->releaseRoom($booking->room_id);

            Cache::forget('layout_notifications');

            return $booking;
        });
    }


    // ─────────────────────────────────────────
    //  DESTROY
    // ─────────────────────────────────────────
    public function destroy(Booking $booking): void
    {
        Cache::forget('layout_notifications');

        DB::transaction(function () use ($booking) {
            if ($booking->status === Booking::STATUS_CONFIRMED || $booking->status === 'confirmed') {
                $this->releaseRoom($booking->room_id);
            }
            $booking->delete();
        });
    }

    // ═════════════════════════════════════════
    //  PRIVATE HELPERS
    // ═════════════════════════════════════════

    private function lockRoom(int $roomId): void
    {
        Room::where('id', $roomId)->update(['status' => 'occupied']);
    }

    private function releaseRoom(int $roomId): void
    {
        $hasOther = Booking::where('room_id', $roomId)
            ->where('status', Booking::STATUS_CONFIRMED)
            ->exists();

        if (! $hasOther) {
            Room::where('id', $roomId)->update(['status' => 'available']);
        }
    }

    private function ensureMeter(
        int $roomId,
        string $type,
        float $ratePerUnit,
        float $taxRate
    ): Meter {
        $meter = Meter::where('room_id', $roomId)
            ->where('type', $type)
            ->first();

        if ($meter instanceof Meter) {
            $meter->update([
                'rate_per_unit' => $ratePerUnit,
                'tax_rate' => $taxRate,
                'is_active' => true,
            ]);
            return $meter;
        }

        $roomNumber = Room::find($roomId)?->room_number ?? (string) $roomId;
        $meterNumber = strtoupper($type[0]) . '-' . $roomNumber . '-' . now()->format('ymd');

        return Meter::create([
            'room_id' => $roomId,
            'type' => $type,
            'meter_number' => $meterNumber,
            'unit' => $type === 'electric' ? 'kWh' : 'หน่วย',
            'rate_per_unit' => $ratePerUnit,
            'tax_rate' => $taxRate,
            'is_active' => true,
            'installed_at' => now()->toDateString(),
        ]);
    }

    private function createInitialReading(
        Meter $meter,
        Booking $booking,
        float $initialValue,
        string $readingDate
    ): void {
        $date = Carbon::parse($readingDate);
        $periodMonth = (int) $date->format('m');
        $periodYear = (int) $date->format('Y');

        $exists = MeterReading::where('meter_id', $meter->id)
            ->where('booking_id', $booking->id)
            ->where('period_month', $periodMonth)
            ->where('period_year', $periodYear)
            ->exists();

        if ($exists) {
            return;
        }

        MeterReading::create([
            'meter_id' => $meter->id,
            'booking_id' => $booking->id,
            'period_month' => $periodMonth,
            'period_year' => $periodYear,
            'reading_date' => $readingDate,
            'reading_value' => $initialValue,
            'recorded_by' => Auth::id(),
            'notes' => 'เลขมิเตอร์เริ่มต้น (เช็คอิน)',
        ]);
    }
}

