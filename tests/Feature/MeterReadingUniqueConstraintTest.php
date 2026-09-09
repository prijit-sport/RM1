<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Meter;
use App\Models\MeterReading;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * เทสยืนยันว่า composite unique index (meter_id, period_month, period_year,
 * booking_id) บน meter_readings ทำงานจริงระดับ DB — เป็น safety net เสริมจาก
 * application-level check ใน MeterBillingService::upsertReading() (updateOrCreate)
 * ที่ไม่ atomic และมีช่องว่างให้เกิด race condition ได้ในทางทฤษฎี
 */
class MeterReadingUniqueConstraintTest extends TestCase
{
    use RefreshDatabase;

    private function createRoom(string $roomNumber): Room
    {
        return Room::create([
            'room_number' => $roomNumber,
            'room_type' => 'Single',
            'price_per_month' => 1500,
            'capacity' => 1,
            'status' => 'occupied',
            'description' => null,
        ]);
    }

    private function createGuest(string $email): Guest
    {
        return Guest::create([
            'first_name' => 'Unique',
            'last_name' => 'Test',
            'email' => $email,
            'phone' => '0800000000',
            'address' => null,
            'city' => null,
            'country' => null,
            'id_number' => 'UNIQ-'.substr(md5($email), 0, 8),
        ]);
    }

    private function createConfirmedBooking(Room $room, Guest $guest): Booking
    {
        return Booking::create([
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addMonths(6)->toDateString(),
            'total_price' => 1500,
            'rent_amount' => 1500,
            'deposit_amount' => 1500,
            'electric_meter_start' => 0,
            'water_meter_start' => 0,
            'status' => 'confirmed',
            'notes' => null,
        ]);
    }

    private function createMeter(Room $room): Meter
    {
        return Meter::create([
            'room_id' => $room->id,
            'type' => 'electric',
            'meter_number' => 'UNIQ-'.$room->id.'-'.now()->format('ymdHis').rand(100, 999),
            'unit' => 'kWh',
            'installed_at' => now()->toDateString(),
            'is_active' => true,
            'notes' => null,
            'rate_per_unit' => 5,
            'tax_rate' => 0,
        ]);
    }

    public function test_duplicate_meter_period_booking_combination_is_rejected_at_db_level(): void
    {
        $room = $this->createRoom('UNIQ-1');
        $guest = $this->createGuest('unique-1@example.com');
        $booking = $this->createConfirmedBooking($room, $guest);
        $meter = $this->createMeter($room);

        MeterReading::create([
            'meter_id' => $meter->id,
            'booking_id' => $booking->id,
            'period_month' => 6,
            'period_year' => 2026,
            'reading_date' => '2026-06-15',
            'reading_value' => 100,
            'recorded_by' => null,
            'notes' => null,
        ]);

        $this->expectException(QueryException::class);

        // แถวที่ 2: meter_id + period_month + period_year + booking_id ซ้ำกับแถวแรก
        // เป๊ะ (วันที่ reading_date ต่างกันเพื่อไม่ชน unique เดิม [meter_id, reading_date])
        MeterReading::create([
            'meter_id' => $meter->id,
            'booking_id' => $booking->id,
            'period_month' => 6,
            'period_year' => 2026,
            'reading_date' => '2026-06-20',
            'reading_value' => 150,
            'recorded_by' => null,
            'notes' => null,
        ]);
    }

    public function test_different_booking_id_for_same_meter_and_period_is_allowed(): void
    {
        // เคสที่ตั้งใจให้ผ่านได้: ห้องเดียวกัน มิเตอร์เดียวกัน รอบบิลเดียวกัน แต่คนละ
        // booking (เช่น ผู้เช่าเก่าย้ายออกกลางเดือน ผู้เช่าใหม่เข้ามาในเดือนเดียวกัน)
        $room = $this->createRoom('UNIQ-2');
        $guestA = $this->createGuest('unique-2a@example.com');
        $guestB = $this->createGuest('unique-2b@example.com');
        $bookingA = $this->createConfirmedBooking($room, $guestA);
        $bookingB = $this->createConfirmedBooking($room, $guestB);
        $meter = $this->createMeter($room);

        $readingA = MeterReading::create([
            'meter_id' => $meter->id,
            'booking_id' => $bookingA->id,
            'period_month' => 7,
            'period_year' => 2026,
            'reading_date' => '2026-07-10',
            'reading_value' => 100,
            'recorded_by' => null,
            'notes' => null,
        ]);

        $readingB = MeterReading::create([
            'meter_id' => $meter->id,
            'booking_id' => $bookingB->id,
            'period_month' => 7,
            'period_year' => 2026,
            'reading_date' => '2026-07-25',
            'reading_value' => 120,
            'recorded_by' => null,
            'notes' => null,
        ]);

        $this->assertNotSame($readingA->id, $readingB->id);
        $this->assertSame(2, MeterReading::where('meter_id', $meter->id)->count());
    }

    public function test_multiple_general_readings_without_period_are_still_allowed(): void
    {
        // reading ทั่วไป (ไม่ผ่าน "บันทึกรายเดือน") ไม่มี period_month/period_year
        // (เป็น NULL) — MySQL unique index ถือว่า NULL แต่ละแถวไม่เท่ากัน จึงไม่ชนกัน
        $room = $this->createRoom('UNIQ-3');
        $meter = $this->createMeter($room);

        MeterReading::create([
            'meter_id' => $meter->id,
            'booking_id' => null,
            'period_month' => null,
            'period_year' => null,
            'reading_date' => '2026-08-01',
            'reading_value' => 50,
            'recorded_by' => null,
            'notes' => null,
        ]);

        MeterReading::create([
            'meter_id' => $meter->id,
            'booking_id' => null,
            'period_month' => null,
            'period_year' => null,
            'reading_date' => '2026-08-02',
            'reading_value' => 55,
            'recorded_by' => null,
            'notes' => null,
        ]);

        $this->assertSame(2, MeterReading::where('meter_id', $meter->id)->count());
    }
}
