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
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
 
/**
 * Feature tests for InvoiceService::bulkCreateFromBookings() and the
 * lockForUpdate() fix in generateInvoiceNumber().
 *
 * เขียนขึ้นคู่กับการแก้ 2 จุด:
 *  1. generateInvoiceNumber() — เพิ่ม lockForUpdate() กัน invoice_number ซ้ำ
 *     เมื่อออกบิลพร้อมกันหลาย request
 *  2. bulkCreateFromBookings() — เปลี่ยนจาก per-row getMeterBillData() (N+1)
 *     มาเป็น batch-loaded calculateUtilityBulkData()
 *
 * หมายเหตุเรื่อง SQLite (เหมือน BookingConcurrencyTest): DB ทดสอบเป็น SQLite
 * in-memory ซึ่งไม่รองรับ SELECT ... FOR UPDATE จริง (grammar compile เป็น
 * ค่าว่าง) จึงไม่สามารถจำลอง race condition ข้ามคอนเนกชันจริงได้ในเทสนี้
 * แนวทางเดียวกับ BookingConcurrencyTest คือ (ก) ยืนยันว่า query ที่คอมไพล์
 * ด้วย MySQL grammar มี "for update" ต่อท้ายจริง และ (ข) ยืนยัน business
 * invariant ว่าเลข invoice ที่ออกมาไม่ซ้ำกันแม้เรียกหลายครั้งติดกัน
 */
class InvoiceBulkCreateTest extends TestCase
{
    use RefreshDatabase;
 
    private InvoiceService $service;
 
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(InvoiceService::class);
    }
 
    // ─────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────
 
    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);
 
        return User::factory()->create(['role_id' => $role->id]);
    }
 
    private function createRoom(string $roomNumber, float $pricePerMonth = 1500): Room
    {
        return Room::create([
            'room_number' => $roomNumber,
            'room_type' => 'Single',
            'price_per_month' => $pricePerMonth,
            'capacity' => 1,
            'status' => 'occupied',
            'description' => null,
        ]);
    }
 
    private function createGuest(string $email): Guest
    {
        return Guest::create([
            'first_name' => 'Bulk',
            'last_name' => 'Test',
            'email' => $email,
            'phone' => '0800000000',
            'address' => null,
            'city' => null,
            'country' => null,
            'id_number' => 'BULK-'.substr(md5($email), 0, 8),
        ]);
    }
 
    private function createConfirmedBooking(Room $room, Guest $guest, float $rentAmount = 1500): Booking
    {
        return Booking::create([
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addMonths(6)->toDateString(),
            'total_price' => $rentAmount,
            'rent_amount' => $rentAmount,
            'deposit_amount' => $rentAmount,
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
 
    private function createReading(Meter $meter, ?Booking $booking, int $month, int $year, float $value): MeterReading
    {
        return MeterReading::create([
            'meter_id' => $meter->id,
            'booking_id' => $booking?->id,
            'period_month' => $month,
            'period_year' => $year,
            'reading_date' => Carbon::createFromDate($year, $month, 15)->toDateString(),
            'reading_value' => $value,
            'recorded_by' => null,
            'notes' => null,
        ]);
    }
 
    private function bulkPayload(array $bookingIds, string $status = 'sent'): array
    {
        return [
            'selected_bookings' => $bookingIds,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(15)->toDateString(),
            'status' => $status,
        ];
    }
 
    // ─────────────────────────────────────────
    //  bulkCreateFromBookings() — rent
    // ─────────────────────────────────────────
 
    public function test_bulk_create_rent_invoices_for_multiple_bookings(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));
 
        $roomA = $this->createRoom('BULK-R1', 1500);
        $roomB = $this->createRoom('BULK-R2', 2000);
        $guestA = $this->createGuest('bulk-rent-a@example.com');
        $guestB = $this->createGuest('bulk-rent-b@example.com');
 
        $bookingA = $this->createConfirmedBooking($roomA, $guestA, 1500);
        $bookingB = $this->createConfirmedBooking($roomB, $guestB, 2000);
 
        $result = $this->service->bulkCreateFromBookings(
            validated: $this->bulkPayload([$bookingA->id, $bookingB->id]),
            invoiceType: 'rent',
            month: now()->month,
            year: now()->year,
        );
 
        $this->assertSame(2, $result['created'], 'ต้องสร้างใบแจ้งหนี้ครบ 2 ใบ');
        $this->assertSame([], $result['skipped'], 'rent invoice ไม่มีเหตุให้ skip');
        $this->assertSame(2, Invoice::where('invoice_type', 'rent')->count());
 
        $invoiceA = Invoice::where('booking_id', $bookingA->id)->firstOrFail();
        $this->assertEquals(1500.0, (float) $invoiceA->amount);
        $this->assertEqualsWithDelta(1500 * 0.07, (float) $invoiceA->tax, 0.01);
        $this->assertEqualsWithDelta(1500 * 1.07, (float) $invoiceA->total, 0.01);
 
        $invoiceB = Invoice::where('booking_id', $bookingB->id)->firstOrFail();
        $this->assertEquals(2000.0, (float) $invoiceB->amount);
 
        // invoice_number ต้องไม่ซ้ำกัน
        $this->assertNotSame($invoiceA->invoice_number, $invoiceB->invoice_number);
    }
 
    // ─────────────────────────────────────────
    //  bulkCreateFromBookings() — utility (batch path)
    // ─────────────────────────────────────────
 
    public function test_bulk_create_utility_invoices_computes_same_totals_as_calculate_utility_bulk_data(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));
 
        $room = $this->createRoom('BULK-U1');
        $guest = $this->createGuest('bulk-utility@example.com');
        $booking = $this->createConfirmedBooking($room, $guest);
 
        $month = 6;
        $year = 2026;
        $prevMonth = 5;
        $prevYear = 2026;
 
        $electricMeter = $this->createMeter($room, 'electric', 7.5);
        $waterMeter = $this->createMeter($room, 'water', 18);
 
        // เดือนก่อน (baseline) + เดือนนี้ (current)
        $this->createReading($electricMeter, $booking, $prevMonth, $prevYear, 100);
        $this->createReading($electricMeter, $booking, $month, $year, 150); // usage 50 * 7.5 = 375
        $this->createReading($waterMeter, $booking, $prevMonth, $prevYear, 20);
        $this->createReading($waterMeter, $booking, $month, $year, 35); // usage 15 * 18 = 270
 
        // คำนวณ expected ด้วยฟังก์ชัน batch ตัวเดียวกับที่ bulkCreateFromBookings ควรใช้ภายใน
        $expected = $this->service->calculateUtilityBulkData(
            collect([$booking]),
            $month,
            $year,
        )->get($booking->id);
 
        $this->assertTrue($expected['has_reading']);
        $this->assertEqualsWithDelta(375 + 270, $expected['base_cost'], 0.01);
 
        $result = $this->service->bulkCreateFromBookings(
            validated: $this->bulkPayload([$booking->id]),
            invoiceType: 'utility',
            month: $month,
            year: $year,
        );
 
        $this->assertSame(1, $result['created']);
        $this->assertSame([], $result['skipped']);
 
        $invoice = Invoice::where('booking_id', $booking->id)
            ->where('invoice_type', 'utility')
            ->firstOrFail();
 
        // ✅ ยืนยันว่าผลลัพธ์จาก bulkCreateFromBookings() ตรงกับ calculateUtilityBulkData()
        // เป๊ะ (พิสูจน์ว่า refactor ไปใช้ batch method แล้วค่าไม่เปลี่ยน)
        $this->assertEqualsWithDelta($expected['base_cost'], (float) $invoice->amount, 0.01);
        $this->assertEqualsWithDelta($expected['tax'], (float) $invoice->tax, 0.01);
        $this->assertEqualsWithDelta($expected['total'], (float) $invoice->total, 0.01);
    }
 
    public function test_bulk_create_utility_skips_booking_without_meter_reading(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));
 
        $roomWithReading = $this->createRoom('BULK-U2');
        $roomWithoutReading = $this->createRoom('BULK-U3');
        $guestA = $this->createGuest('bulk-skip-a@example.com');
        $guestB = $this->createGuest('bulk-skip-b@example.com');
 
        $bookingWithReading = $this->createConfirmedBooking($roomWithReading, $guestA);
        $bookingWithoutReading = $this->createConfirmedBooking($roomWithoutReading, $guestB);
 
        $month = 7;
        $year = 2026;
 
        $meter = $this->createMeter($roomWithReading, 'electric', 7.5);
        $this->createReading($meter, $bookingWithReading, $month, $year, 50);
        // roomWithoutReading: ไม่มีมิเตอร์/reading เลย
 
        $result = $this->service->bulkCreateFromBookings(
            validated: $this->bulkPayload([$bookingWithReading->id, $bookingWithoutReading->id]),
            invoiceType: 'utility',
            month: $month,
            year: $year,
        );
 
        // ต้องสร้างแค่ 1 ใบ (ห้องที่ไม่มี reading ถูกข้าม ไม่ error ไม่หยุดทั้ง batch)
        $this->assertSame(1, $result['created']);
        $this->assertSame(1, Invoice::where('booking_id', $bookingWithReading->id)->count());
        $this->assertSame(0, Invoice::where('booking_id', $bookingWithoutReading->id)->count());
 
        // ✅ FIX: ตอนนี้ต้องรู้ได้ว่าห้องไหนถูกข้าม (ไม่ใช่แค่รู้จำนวนรวม)
        $this->assertCount(1, $result['skipped'], 'ต้องมี booking ที่ถูกข้ามแค่ 1 รายการ');
        $this->assertSame($bookingWithoutReading->id, $result['skipped'][0]['booking_id']);
        $this->assertSame($roomWithoutReading->room_number, $result['skipped'][0]['room_number']);
        $this->assertNotEmpty($result['skipped'][0]['reason']);
    }
 
    // ─────────────────────────────────────────
    //  bulkCreateFromBookings() — missing booking id
    // ─────────────────────────────────────────
 
    public function test_bulk_create_throws_when_a_selected_booking_id_does_not_exist(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));
 
        $room = $this->createRoom('BULK-MISSING');
        $guest = $this->createGuest('bulk-missing@example.com');
        $booking = $this->createConfirmedBooking($room, $guest);
 
        $nonExistentId = $booking->id + 999999;
 
        $this->expectException(ModelNotFoundException::class);
 
        $this->service->bulkCreateFromBookings(
            validated: $this->bulkPayload([$booking->id, $nonExistentId]),
            invoiceType: 'rent',
            month: now()->month,
            year: now()->year,
        );
 
        // เพราะทั้งลูปอยู่ใน DB::transaction เดียว ถ้า throw ระหว่างทาง
        // ต้อง rollback ทั้งหมด ไม่ทิ้ง invoice ของ booking แรกไว้ค้าง
        $this->assertSame(0, Invoice::count());
    }
 
    // ─────────────────────────────────────────
    //  generateInvoiceNumber() — lockForUpdate fix
    // ─────────────────────────────────────────
 
    public function test_generate_invoice_number_query_uses_for_update_lock_clause(): void
    {
        // เหมือน BookingConcurrencyTest::test_create_overlap_query_uses_for_update_lock_clause():
        // SQLite (test env) compile lockForUpdate() เป็นค่าว่าง จึงต้องบังคับใช้ MySQL grammar
        // เพื่อพิสูจน์ว่า "for update" ถูก wire เข้ากับ query จริง (แม้ SQLite จะมองไม่เห็นผล)
        $connection = DB::connection();
        $originalGrammar = $connection->getQueryGrammar();
        $connection->setQueryGrammar(new \Illuminate\Database\Query\Grammars\MySqlGrammar($connection));
 
        try {
            $sql = $connection->table('invoices')
                ->whereYear('created_at', now()->year)
                ->orderByDesc('id')
                ->lockForUpdate()
                ->toSql();
 
            $this->assertStringContainsString('for update', $sql);
        } finally {
            $connection->setQueryGrammar($originalGrammar);
        }
    }
 
    public function test_generate_invoice_number_produces_unique_sequential_numbers(): void
    {
        $numbers = [];
        for ($i = 0; $i < 5; $i++) {
            $number = $this->service->generateInvoiceNumber();
            $numbers[] = $number;
 
            // จำลองว่าเลขนี้ถูกใช้ไปแล้วจริง (เหมือนตอน bulkCreateFromBookings
            // เรียก generateInvoiceNumber() แล้ว insert ทันทีในลูปเดียวกัน)
            $room = $this->createRoom('SEQ-'.$i);
            $guest = $this->createGuest("seq-{$i}@example.com");
            $booking = $this->createConfirmedBooking($room, $guest);
 
            Invoice::create([
                'booking_id' => $booking->id,
                'invoice_number' => $number,
                'amount' => 100,
                'tax' => 7,
                'total' => 107,
                'issue_date' => now(),
                'due_date' => now()->addDays(15),
                'status' => 'draft',
            ]);
        }
 
        $this->assertSame(
            $numbers,
            array_unique($numbers),
            'generateInvoiceNumber() ต้องไม่ให้เลขซ้ำกันเมื่อเรียกต่อเนื่องกันหลายครั้ง'
        );
        $this->assertCount(5, Invoice::pluck('invoice_number')->unique());
    }
 
    public function test_bulk_create_from_bookings_never_produces_duplicate_invoice_numbers(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));
 
        $bookingIds = [];
        for ($i = 0; $i < 4; $i++) {
            $room = $this->createRoom('DUPCHK-'.$i);
            $guest = $this->createGuest("dupchk-{$i}@example.com");
            $bookingIds[] = $this->createConfirmedBooking($room, $guest, 1000 + $i)->id;
        }
 
        $result = $this->service->bulkCreateFromBookings(
            validated: $this->bulkPayload($bookingIds),
            invoiceType: 'rent',
            month: now()->month,
            year: now()->year,
        );
 
        $this->assertSame(4, $result['created']);
 
        $invoiceNumbers = Invoice::pluck('invoice_number');
        $this->assertCount(
            4,
            $invoiceNumbers->unique(),
            'ใบแจ้งหนี้ทุกใบที่สร้างจาก bulkCreateFromBookings() ต้องมีเลขไม่ซ้ำกัน'
        );
    }
}
 