<?php
 
namespace Tests\Feature;
 
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Invoice;
use App\Models\Meter;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
 
/**
 * End-to-end test: บันทึกมิเตอร์ -> สร้าง draft invoice -> ยืนยันผ่านฟอร์ม -> เช็คผลลัพธ์
 *
 * ครอบคลุม HTTP flow เต็มรูปแบบที่ผู้ใช้จริงเจอ ซึ่งต่างจาก MeterBillingTest เดิม
 * ที่เรียก MeterBillingService ตรงๆ (ข้าม Controller/Request validation ไปเลย)
 * เทสนี้เพิ่มมาเพื่อจับบั๊กที่ MeterBillingTest เดิมมองไม่เห็น เช่น
 * StoreInvoiceRequest::rules() ที่เคย reject invoice_number ของ draft ตัวเอง
 */
class MeterToInvoiceEndToEndTest extends TestCase
{
    use RefreshDatabase;
 
    public function test_full_flow_record_meter_reading_then_confirm_invoice_via_http(): void
    {
        $admin = $this->createUserWithRole('Admin');
        $this->actingAs($admin);
 
        $room = Room::create([
            'room_number' => 'E2E-101',
            'room_type' => 'Single',
            'price_per_month' => 1500,
            'capacity' => 1,
            'status' => 'occupied',
            'description' => null,
        ]);
 
        $guest = Guest::create([
            'first_name' => 'Somchai',
            'last_name' => 'Testcase',
            'email' => 'e2e-somchai@example.com',
            'phone' => '0811111111',
            'address' => null,
            'city' => null,
            'country' => null,
            'id_number' => 'E2E-ID-0001',
        ]);
 
        $booking = Booking::create([
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addMonths(6)->toDateString(),
            'total_price' => 1500,
            'rent_amount' => 1500,
            'deposit_amount' => 1500,
            'electric_meter_start' => 100,
            'water_meter_start' => 50,
            'status' => 'confirmed',
            'notes' => null,
        ]);
 
        $meter = Meter::create([
            'room_id' => $room->id,
            'type' => 'electric',
            'meter_number' => 'E2E-METER-' . now()->format('ymdHis'),
            'unit' => 'kWh',
            'installed_at' => now()->toDateString(),
            'is_active' => true,
            'notes' => null,
            'rate_per_unit' => 7.5,
            'tax_rate' => 7,
        ]);
 
        // Step 1: บันทึกมิเตอร์ผ่าน HTTP endpoint จริง (ไม่เรียก Service ตรงๆ)
        $recordResponse = $this->post(
            route('meters.readings.monthly', $meter),
            [
                'period_month' => 6,
                'period_year' => 2026,
                'reading_value' => 250,
                'notes' => 'e2e test reading',
            ]
        );
 
        // ต้อง redirect ไปหน้า invoices.create พร้อม from_meter=1 และ invoice_id
        $recordResponse->assertRedirect();
        $this->assertStringContainsString('from_meter=1', $recordResponse->headers->get('Location'));
 
        /** @var Invoice $draftInvoice */
        $draftInvoice = Invoice::where('booking_id', $booking->id)
            ->where('status', 'draft')
            ->firstOrFail();
 
        $this->assertNotEmpty($draftInvoice->invoice_number, 'Draft invoice ต้องมี invoice_number ตั้งแต่สร้าง');
        $this->assertSame('utility', $draftInvoice->invoice_type, 'Invoice จาก meter ต้องเป็นประเภท utility');
 
        // Step 2: เปิดหน้า invoices.create แบบมี from_meter เพื่อดูว่าฟอร์ม pre-fill ถูกต้อง
        $createPageResponse = $this->get(route('invoices.create', [
            'from_meter' => 1,
            'invoice_id' => $draftInvoice->id,
        ]));
 
        $createPageResponse->assertOk();
        $createPageResponse->assertSee($draftInvoice->invoice_number);
 
        // Step 3: ยืนยันใบแจ้งหนี้ผ่านฟอร์ม (จุดที่เคย fail เพราะ invoice_number ซ้ำกับตัวเอง)
        $confirmResponse = $this->post(route('invoices.store'), [
            'draft_invoice_id' => $draftInvoice->id,
            'booking_id' => $booking->id,
            'invoice_number' => $draftInvoice->invoice_number, // เลขเดิมของ draft ตัวเอง
            'invoice_type' => 'utility',
            'amount' => $draftInvoice->amount ?? 1000,
            'tax' => $draftInvoice->tax ?? 70,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(15)->toDateString(),
            'status' => 'sent',
            'notes' => 'confirmed via e2e test',
        ]);
 
        // ต้อง redirect ไปหน้า invoices.show ไม่ใช่ back() พร้อม validation error
        $confirmResponse->assertSessionHasNoErrors();
        $confirmResponse->assertRedirect(route('invoices.show', $draftInvoice));
 
        // Step 4: ยืนยันว่า invoice ถูกอัปเดตสถานะจริง ไม่ใช่สร้างแถวใหม่ซ้อน
        $this->assertSame(
            1,
            Invoice::where('booking_id', $booking->id)->count(),
            'ต้องมี invoice แค่ 1 แถวเท่านั้น ไม่ใช่สร้างแถวใหม่ซ้อนกับ draft เดิม'
        );
 
        $draftInvoice->refresh();
        $this->assertSame('sent', $draftInvoice->status);
        $this->assertSame('confirmed via e2e test', $draftInvoice->notes);
 
        // Step 5: เช็คว่า invoice ปรากฏถูกต้องในหน้า index พร้อม filter invoice_type
        $indexResponse = $this->get(route('invoices.index', ['invoice_type' => 'utility']));
        $indexResponse->assertOk();
        $indexResponse->assertSeeText($draftInvoice->invoice_number);
 
        // Step 6: ทดสอบต่อว่า markAsPaid ใช้งานได้กับ invoice ที่มาจาก meter reading
        $draftInvoice->update(['status' => 'sent']);
        $payResponse = $this->post(route('invoices.markAsPaid', $draftInvoice));
        $payResponse->assertRedirect(route('invoices.show', $draftInvoice));
 
        $draftInvoice->refresh();
        $this->assertSame('paid', $draftInvoice->status);
    }
 
    public function test_confirming_second_month_reading_does_not_reuse_first_invoice_number(): void
    {
        // ป้องกัน regression: ถ้ามีการ generate invoice_number ใหม่ทุกเดือน
        // ต้องไม่ชนกับเลขเดือนก่อนหน้าที่ confirm ไปแล้ว
        $admin = $this->createUserWithRole('Admin');
        $this->actingAs($admin);
 
        $room = Room::create([
            'room_number' => 'E2E-102',
            'room_type' => 'Single',
            'price_per_month' => 1500,
            'capacity' => 1,
            'status' => 'occupied',
            'description' => null,
        ]);
 
        $guest = Guest::create([
            'first_name' => 'Suda',
            'last_name' => 'Testcase',
            'email' => 'e2e-suda@example.com',
            'phone' => '0822222222',
            'address' => null,
            'city' => null,
            'country' => null,
            'id_number' => 'E2E-ID-0002',
        ]);
 
        $booking = Booking::create([
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addMonths(6)->toDateString(),
            'total_price' => 1500,
            'rent_amount' => 1500,
            'deposit_amount' => 1500,
            'electric_meter_start' => 100,
            'water_meter_start' => 50,
            'status' => 'confirmed',
            'notes' => null,
        ]);
 
        $meter = Meter::create([
            'room_id' => $room->id,
            'type' => 'water',
            'meter_number' => 'E2E-METER-W-' . now()->format('ymdHis'),
            'unit' => 'm3',
            'installed_at' => now()->toDateString(),
            'is_active' => true,
            'notes' => null,
            'rate_per_unit' => 18,
            'tax_rate' => 0,
        ]);
 
        // เดือนแรก: บันทึก + ยืนยัน
        $this->post(route('meters.readings.monthly', $meter), [
            'period_month' => 1,
            'period_year' => 2026,
            'reading_value' => 10,
        ]);
        $invoiceMonth1 = Invoice::where('booking_id', $booking->id)->latest('id')->first();
 
        $this->assertNotNull($invoiceMonth1, 'ต้องมี invoice ของเดือนแรกถูกสร้าง');
 
        $this->post(route('invoices.store'), [
            'draft_invoice_id' => $invoiceMonth1->id,
            'booking_id' => $invoiceMonth1->booking_id,
            'invoice_number' => $invoiceMonth1->invoice_number,
            'invoice_type' => 'utility',
            'amount' => 100,
            'tax' => 0,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(15)->toDateString(),
            'status' => 'sent',
        ]);
 
        // เดือนที่สอง: บันทึกใหม่ ต้องได้ invoice คนละใบ เลขไม่ซ้ำกับเดือนแรก
        $this->post(route('meters.readings.monthly', $meter), [
            'period_month' => 2,
            'period_year' => 2026,
            'reading_value' => 20,
        ]);
 
        $invoiceMonth2 = Invoice::where('room_id', $room->id)
            ->where('id', '!=', $invoiceMonth1->id)
            ->latest('id')->first();
 
        $this->assertNotNull($invoiceMonth2, 'ต้องมี invoice ของเดือนที่สองแยกจากเดือนแรก');
        $this->assertNotSame(
            $invoiceMonth1->invoice_number,
            $invoiceMonth2->invoice_number,
            'invoice_number ของแต่ละเดือนต้องไม่ซ้ำกัน'
        );
 
        $confirmResponse = $this->post(route('invoices.store'), [
            'draft_invoice_id' => $invoiceMonth2->id,
            'booking_id' => $invoiceMonth2->booking_id,
            'invoice_number' => $invoiceMonth2->invoice_number,
            'invoice_type' => 'utility',
            'amount' => 180,
            'tax' => 0,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(15)->toDateString(),
            'status' => 'sent',
        ]);
 
        $confirmResponse->assertSessionHasNoErrors();
    }
 
    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);
        return User::factory()->create(['role_id' => $role->id]);
    }
}
 