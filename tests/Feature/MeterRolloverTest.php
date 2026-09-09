<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Invoice;
use App\Models\Meter;
use App\Models\MeterReading;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\MeterBillingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests สำหรับการแก้ปัญหา meter rollover (มิเตอร์ถูกเปลี่ยนตัวใหม่)
 * ครอบคลุมทั้ง flow ผ่าน MeterBillingService (หน้า record monthly) และ
 * InvoiceService::bulkCreateFromBookings() (หน้า bulk-create utility invoice)
 */
class MeterRolloverTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }

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
            'first_name' => 'Rollover',
            'last_name' => 'Test',
            'email' => $email,
            'phone' => '0800000000',
            'address' => null,
            'city' => null,
            'country' => null,
            'id_number' => 'ROLL-'.substr(md5($email), 0, 8),
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

    private function createMeter(Room $room, string $type, float $ratePerUnit): Meter
    {
        return Meter::create([
            'room_id' => $room->id,
            'type' => $type,
            'meter_number' => strtoupper($type[0]).'-'.$room->id.'-'.now()->format('ymdHis').rand(100, 999),
            'unit' => $type === 'electric' ? 'kWh' : 'm3',
            'installed_at' => now()->toDateString(),
            'is_active' => true,
            'notes' => null,
            'rate_per_unit' => $ratePerUnit,
            'tax_rate' => 0,
        ]);
    }

    private function createReading(
        Meter $meter,
        ?Booking $booking,
        int $month,
        int $year,
        float $value,
        bool $isMeterReset = false
    ): MeterReading {
        return MeterReading::create([
            'meter_id' => $meter->id,
            'booking_id' => $booking?->id,
            'period_month' => $month,
            'period_year' => $year,
            'reading_date' => Carbon::createFromDate($year, $month, 15)->toDateString(),
            'reading_value' => $value,
            'is_meter_reset' => $isMeterReset,
            'recorded_by' => null,
            'notes' => null,
        ]);
    }

    // ─────────────────────────────────────────
    //  MeterBillingService::compute() / calculateMonthlyBreakdown()
    // ─────────────────────────────────────────

    public function test_meter_billing_service_computes_zero_usage_on_unflagged_rollover(): void
    {
        $room = $this->createRoom('ROLL-1');
        $meter = $this->createMeter($room, 'electric', 7.5);

        $previous = new MeterReading(['reading_value' => 9800]);
        $current = new MeterReading(['reading_value' => 45, 'is_meter_reset' => false]);

        $service = new MeterBillingService;
        $result = $service->compute($meter, $previous, $current);

        // พฤติกรรมเดิม (ปลอดภัยไว้ก่อน): ไม่ติ๊ก reset = usage เป็น 0
        $this->assertSame(0.0, $result['usage']);
        $this->assertTrue($result['unflagged_rollover'], 'ต้อง flag ว่าเป็น rollover ที่ยังไม่ได้ตรวจสอบ');
    }

    public function test_meter_billing_service_computes_correct_usage_when_flagged_as_reset(): void
    {
        $room = $this->createRoom('ROLL-2');
        $meter = $this->createMeter($room, 'electric', 7.5);

        $previous = new MeterReading(['reading_value' => 9800]);
        $current = new MeterReading(['reading_value' => 45, 'is_meter_reset' => true]);

        $service = new MeterBillingService;
        $result = $service->compute($meter, $previous, $current);

        // ✅ FIX: ติ๊ก reset แล้ว usage ต้องคำนวณจากเลขที่กรอกทั้งหมด ไม่ใช่ 0
        $this->assertSame(45.0, $result['usage']);
        $this->assertEqualsWithDelta(45 * 7.5, $result['base'], 0.01);
        $this->assertTrue($result['is_meter_reset']);
        $this->assertFalse($result['unflagged_rollover']);
    }

    public function test_record_monthly_and_create_invoice_persists_is_meter_reset_flag(): void
    {
        $this->actingAs($this->createUserWithRole('Staff'));

        $room = $this->createRoom('ROLL-3');
        $guest = $this->createGuest('rollover-monthly@example.com');
        $booking = $this->createConfirmedBooking($room, $guest);
        $meter = $this->createMeter($room, 'electric', 2.5);

        $service = new MeterBillingService;

        // เดือนแรก: reading ปกติ
        $result1 = $service->recordMonthlyAndCreateInvoice($meter, 1, 2026, 9800);
        $this->assertTrue($result1['success']);

        // เดือนถัดมา: มิเตอร์ถูกเปลี่ยนใหม่ เลขรีเซ็ตกลับไปต่ำกว่าเดิมมาก
        $result2 = $service->recordMonthlyAndCreateInvoice(
            $meter,
            2,
            2026,
            45,
            'เปลี่ยนมิเตอร์ตัวใหม่',
            isMeterReset: true,
        );

        $this->assertTrue($result2['success']);

        $this->assertDatabaseHas('meter_readings', [
            'meter_id' => $meter->id,
            'period_month' => 2,
            'period_year' => 2026,
            'reading_value' => 45,
            'is_meter_reset' => true,
        ]);

        // usage ของเดือนที่ 2 ต้องคำนวณจาก 45 หน่วยตรง ๆ (45 * 2.5 = 112.5)
        // ไม่ใช่ max(0, 45 - 9800) = 0
        $this->assertEqualsWithDelta(112.5, $result2['totals']['electric'], 0.01);
    }

    // ─────────────────────────────────────────
    //  InvoiceService::calculateUtilityBulkData() / bulkCreateFromBookings()
    // ─────────────────────────────────────────

    public function test_calculate_utility_bulk_data_flags_unflagged_rollover(): void
    {
        $room = $this->createRoom('ROLL-BULK-1');
        $guest = $this->createGuest('rollover-bulk-1@example.com');
        $booking = $this->createConfirmedBooking($room, $guest);

        $meter = $this->createMeter($room, 'electric', 7.5);
        $this->createReading($meter, $booking, 5, 2026, 9800);
        // เดือนนี้: เลขย้อนกลับ แต่ไม่ได้ติ๊ก is_meter_reset (พลาด/ลืมติ๊ก)
        $this->createReading($meter, $booking, 6, 2026, 45, isMeterReset: false);

        $service = app(InvoiceService::class);
        $result = $service->calculateUtilityBulkData(collect([$booking]), 6, 2026)->get($booking->id);

        $this->assertSame(0.0, $result['electric']['usage']);
        $this->assertTrue($result['electric']['unflagged_rollover']);
    }

    public function test_calculate_utility_bulk_data_computes_usage_correctly_when_meter_reset_flagged(): void
    {
        $room = $this->createRoom('ROLL-BULK-2');
        $guest = $this->createGuest('rollover-bulk-2@example.com');
        $booking = $this->createConfirmedBooking($room, $guest);

        $meter = $this->createMeter($room, 'electric', 7.5);
        $this->createReading($meter, $booking, 5, 2026, 9800);
        // เดือนนี้: มิเตอร์เปลี่ยนใหม่จริง ติ๊ก is_meter_reset ไว้ถูกต้อง
        $this->createReading($meter, $booking, 6, 2026, 45, isMeterReset: true);

        $service = app(InvoiceService::class);
        $result = $service->calculateUtilityBulkData(collect([$booking]), 6, 2026)->get($booking->id);

        // ✅ FIX: usage = 45 (เลขที่กรอกทั้งหมด) ไม่ใช่ 0
        $this->assertSame(45.0, $result['electric']['usage']);
        $this->assertEqualsWithDelta(45 * 7.5, $result['electric']['cost'], 0.01);
        $this->assertFalse($result['electric']['unflagged_rollover']);
    }

    public function test_bulk_create_from_bookings_still_creates_invoice_with_zero_usage_on_unflagged_rollover(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $room = $this->createRoom('ROLL-BULK-3');
        $guest = $this->createGuest('rollover-bulk-3@example.com');
        $booking = $this->createConfirmedBooking($room, $guest);

        $month = 6;
        $year = 2026;

        $meter = $this->createMeter($room, 'electric', 7.5);
        $this->createReading($meter, $booking, 5, $year, 9800);
        $this->createReading($meter, $booking, $month, $year, 45, isMeterReset: false);

        $service = app(InvoiceService::class);
        $result = $service->bulkCreateFromBookings(
            validated: [
                'selected_bookings' => [$booking->id],
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(15)->toDateString(),
                'status' => 'draft',
            ],
            invoiceType: 'utility',
            month: $month,
            year: $year,
        );

        // ยังคงสร้าง invoice ได้ (ไม่ throw / ไม่ skip) เพราะมี reading อยู่ — แค่ usage
        // คำนวณเป็น 0 อย่างปลอดภัยไว้ก่อน แล้วแจ้งเตือนแยกผ่าน rollover_warnings
        $this->assertSame(1, $result['created']);
        $this->assertSame([], $result['skipped']);

        $invoice = Invoice::where('booking_id', $booking->id)->firstOrFail();
        $this->assertSame(0.0, (float) $invoice->amount);

        // ✅ FIX: ต้องมีคำเตือนเรื่อง unflagged rollover ให้ผู้ใช้ไปตรวจสอบ
        $this->assertCount(1, $result['rollover_warnings']);
        $this->assertSame($booking->id, $result['rollover_warnings'][0]['booking_id']);
        $this->assertSame($room->room_number, $result['rollover_warnings'][0]['room_number']);
        $this->assertContains('ไฟฟ้า', $result['rollover_warnings'][0]['meter_types']);
    }

    public function test_bulk_create_from_bookings_computes_correct_amount_when_meter_reset_flagged(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $room = $this->createRoom('ROLL-BULK-4');
        $guest = $this->createGuest('rollover-bulk-4@example.com');
        $booking = $this->createConfirmedBooking($room, $guest);

        $month = 6;
        $year = 2026;

        $meter = $this->createMeter($room, 'electric', 7.5);
        $this->createReading($meter, $booking, 5, $year, 9800);
        $this->createReading($meter, $booking, $month, $year, 45, isMeterReset: true);

        $service = app(InvoiceService::class);
        $result = $service->bulkCreateFromBookings(
            validated: [
                'selected_bookings' => [$booking->id],
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(15)->toDateString(),
                'status' => 'draft',
            ],
            invoiceType: 'utility',
            month: $month,
            year: $year,
        );

        $this->assertSame(1, $result['created']);
        $this->assertSame([], $result['rollover_warnings'], 'ติ๊ก reset ถูกต้องแล้ว ไม่ควรมี warning');

        $invoice = Invoice::where('booking_id', $booking->id)->firstOrFail();
        // ✅ FIX: amount ต้องคำนวณจาก usage = 45 (45 * 7.5 = 337.5) ไม่ใช่ 0
        $this->assertEqualsWithDelta(45 * 7.5, (float) $invoice->amount, 0.01);
    }
}
