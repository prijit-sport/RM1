<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Invoice;
use App\Models\Meter;
use App\Models\MeterReading;
use App\Models\Room;
use App\Models\User;
use App\Models\Role;
use App\Services\MeterBillingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeterBillingTest extends TestCase
{
    use RefreshDatabase;

    public function test_record_monthly_creates_meter_reading(): void
    {
        $this->actingAs($this->createUserWithRole('Manager'));

        $room = Room::create([
            'room_number' => 'A101',
            'room_type' => 'Single',
            'price_per_month' => 1000,
            'capacity' => 1,
            'status' => 'occupied',
            'description' => null,
        ]);

        $guest = Guest::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '0800000000',
            'address' => null,
            'city' => null,
            'country' => null,
            'id_number' => 'ID-1001',
        ]);

        $booking = Booking::create([
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addDays(10)->toDateString(),
            'total_price' => 2000,
            'rent_amount' => 1000,
            'deposit_amount' => 1000,
            'electric_meter_start' => 100,
            'water_meter_start' => 50,
            'status' => 'confirmed',
            'notes' => null,
        ]);

        $meter = Meter::create([
            'room_id' => $room->id,
            'type' => 'electric',
            'meter_number' => 'E-A101-'.now()->format('ymd'),
            'unit' => 'kWh',
            'installed_at' => now()->toDateString(),
            'is_active' => true,
            'notes' => null,
            'rate_per_unit' => 2.5,
            'tax_rate' => 7,
        ]);

        $service = new MeterBillingService();

        $result = $service->recordMonthlyAndCreateInvoice(
            $meter,
            1,
            2026,
            150,
            'test notes'
        );

        $this->assertTrue($result['success']);

        $this->assertDatabaseHas('meter_readings', [
            'meter_id' => $meter->id,
            'booking_id' => $booking->id,
            'period_month' => 1,
            'period_year' => 2026,
            'reading_value' => 150,
        ]);

        /** @var Invoice $invoice */
        $invoice = $result['invoice'];
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertSame('draft', $invoice->status);
    }

    public function test_record_monthly_updates_existing_invoice(): void
    {
        $this->actingAs($this->createUserWithRole('Manager'));

        $room = Room::create([
            'room_number' => 'A202',
            'room_type' => 'Single',
            'price_per_month' => 1000,
            'capacity' => 1,
            'status' => 'occupied',
            'description' => null,
        ]);

        $guest = Guest::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'phone' => '0800000000',
            'address' => null,
            'city' => null,
            'country' => null,
            'id_number' => 'ID-1002',
        ]);

        $booking = Booking::create([
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addDays(10)->toDateString(),
            'total_price' => 2000,
            'rent_amount' => 1000,
            'deposit_amount' => 1000,
            'electric_meter_start' => 100,
            'water_meter_start' => 50,
            'status' => 'confirmed',
            'notes' => null,
        ]);

        $meter = Meter::create([
            'room_id' => $room->id,
            'type' => 'electric',
            'meter_number' => 'E-A202-'.now()->format('ymd'),
            'unit' => 'kWh',
            'installed_at' => now()->toDateString(),
            'is_active' => true,
            'notes' => null,
            'rate_per_unit' => 2.0,
            'tax_rate' => 0,
        ]);

        $service = new MeterBillingService();

        $result1 = $service->recordMonthlyAndCreateInvoice($meter, 2, 2026, 120, null);
        $this->assertTrue($result1['success']);

        $result2 = $service->recordMonthlyAndCreateInvoice($meter, 2, 2026, 140, null);
        $this->assertTrue($result2['success']);

        $this->assertSame(1, Invoice::query()
            ->where('booking_id', $booking->id)
            ->where('room_id', $room->id)
            ->where('guest_id', $guest->id)
            ->whereMonth('issue_date', 2)
            ->whereYear('issue_date', 2026)
            ->count());

        $this->assertDatabaseHas('invoices', [
            'booking_id' => $booking->id,
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'status' => 'draft',
        ]);
    }

    public function test_record_monthly_fails_without_active_booking(): void
    {
        $this->actingAs($this->createUserWithRole('Manager'));

        $room = Room::create([
            'room_number' => 'A303',
            'room_type' => 'Single',
            'price_per_month' => 1000,
            'capacity' => 1,
            'status' => 'available',
            'description' => null,
        ]);

        $guest = Guest::create([
            'first_name' => 'Bob',
            'last_name' => 'Taylor',
            'email' => 'bob@example.com',
            'phone' => '0800000000',
            'address' => null,
            'city' => null,
            'country' => null,
            'id_number' => 'ID-1003',
        ]);

        // Meter exists but no confirmed booking for this room
        $meter = Meter::create([
            'room_id' => $room->id,
            'type' => 'electric',
            'meter_number' => 'E-A303-'.now()->format('ymd'),
            'unit' => 'kWh',
            'installed_at' => now()->toDateString(),
            'is_active' => true,
            'notes' => null,
            'rate_per_unit' => 2.5,
            'tax_rate' => 7,
        ]);

        $service = new MeterBillingService();

        $result = $service->recordMonthlyAndCreateInvoice(
            $meter,
            3,
            2026,
            100,
            null
        );

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);
        return User::factory()->create(['role_id' => $role->id]);
    }
}

