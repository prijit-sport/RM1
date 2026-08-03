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

class BusinessLogicRiskTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_marking_invoice_as_paid_updates_payment_details(): void
    {
        $user = $this->createUserWithRole('Staff');
        $this->actingAs($user);

        $guest = Guest::create([
            'first_name' => 'Siri',
            'last_name' => 'Kitt',
            'email' => 'siri@example.com',
            'phone' => '0812345678',
            'id_number' => 'ID-SIRI-001',
        ]);

        $room = Room::create([
            'room_number' => '101',
            'room_type' => 'single',
            'price_per_month' => 5000,
            'capacity' => 1,
            'status' => 'available',
        ]);

        $booking = Booking::create([
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'check_in_date' => Carbon::now()->startOfMonth()->toDateString(),
            'rent_amount' => 5000,
            'deposit_amount' => 5000,
            'total_price' => 5000,
            'electric_meter_start' => 100,
            'water_meter_start' => 50,
            'status' => 'confirmed',
        ]);

        $invoice = Invoice::create([
            'booking_id' => $booking->id,
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'invoice_number' => 'INV-TEST-001',
            'amount' => 100,
            'tax' => 7,
            'total' => 107,
            'issue_date' => Carbon::now()->subDay()->toDateString(),
            'due_date' => Carbon::now()->addDay()->toDateString(),
            'status' => 'sent',
        ]);

        $response = $this->post(route('invoices.markAsPaid', $invoice));

        $response->assertRedirect(route('invoices.show', $invoice));

        $invoice->refresh();

        $this->assertSame('paid', $invoice->status);
        $this->assertSame('cash', $invoice->payment_method);
        $this->assertNotNull($invoice->payment_date);
        $this->assertSame(107.0, (float) $invoice->paid_amount);
    }

    public function test_record_monthly_and_create_invoice_stores_tax_separately_from_total(): void
    {
        $user = $this->createUserWithRole('Staff');
        $this->actingAs($user);

        $guest = Guest::create([
            'first_name' => 'Narin',
            'last_name' => 'Srisai',
            'email' => 'narin@example.com',
            'phone' => '0823456789',
            'id_number' => 'ID-NARIN-001',
        ]);

        $room = Room::create([
            'room_number' => '202',
            'room_type' => 'double',
            'price_per_month' => 7000,
            'capacity' => 2,
            'status' => 'available',
        ]);

        $booking = Booking::create([
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'check_in_date' => Carbon::now()->startOfMonth()->toDateString(),
            'rent_amount' => 7000,
            'deposit_amount' => 7000,
            'total_price' => 7000,
            'electric_meter_start' => 100,
            'water_meter_start' => 20,
            'status' => 'confirmed',
        ]);

        Meter::create([
            'room_id' => $room->id,
            'type' => 'electric',
            'meter_number' => 'ELEC-202',
            'unit' => 'kWh',
            'rate_per_unit' => 5,
            'tax_rate' => 10,
            'is_active' => true,
        ]);

        $service = app(\App\Services\MeterBillingService::class);

        $result = $service->recordMonthlyAndCreateInvoice(
            Meter::where('room_id', $room->id)->where('type', 'electric')->firstOrFail(),
            Carbon::now()->month,
            Carbon::now()->year,
            120,
            'monthly reading'
        );

        $this->assertTrue($result['success']);

        $invoice = $result['invoice'];

        $this->assertSame(100.0, (float) $invoice->amount);
        $this->assertSame(10.0, (float) $invoice->tax);
        $this->assertSame(110.0, (float) $invoice->total);
    }
}
