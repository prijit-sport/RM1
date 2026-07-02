<?php

namespace Tests\Feature;

use App\Models\Meter;
use App\Models\MeterReading;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeterReadingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_ok_for_admin_and_shows_readings(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $room = $this->createRoomForMeters('M201', 'D', 1);
        $meter = $this->createMeterForRoom($room, 'water');
        $reading = $this->createMeterReading($meter, '2026-01-01', 123.45, 'note-1');

        $response = $this->get(route('meters.readings.index', $meter));

        $response->assertOk();
        $response->assertViewIs('meter_readings.index');
        $response->assertSeeText($room->room_number);
        $response->assertSeeText('น้ำ');
        $response->assertSeeText('01/01/2026');
        $response->assertSeeText('123.45');
        $response->assertSeeText($reading->notes);
    }

    public function test_index_returns_forbidden_for_non_admin(): void
    {
        $this->actingAs($this->createUserWithRole('User'));

        $room = $this->createRoomForMeters('M202', 'E', 2);
        $meter = $this->createMeterForRoom($room, 'electric');

        $response = $this->get(route('meters.readings.index', $meter));
        $response->assertStatus(403);
    }

    public function test_store_validation_fail_returns_500_when_payload_invalid(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $room = $this->createRoomForMeters('M203', 'F', 2);
        $meter = $this->createMeterForRoom($room, 'electric');

        // invalid reading_value (min:0, numeric)
        $response = $this->from(route('meters.readings.create', $meter))
            ->post(route('meters.readings.store', $meter), [
                'reading_date' => '2026-01-01',
                'reading_value' => -1,
                'notes' => 'bad',
            ]);

        $response->assertStatus(500);
        $this->assertSame(0, MeterReading::count());
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    private function createRoomForMeters(string $roomNumber, string $zone, ?int $floor): Room
    {
        return Room::create([
            'room_number' => $roomNumber,
            'room_type' => 'Single',
            'zone' => $zone,
            'floor' => $floor,
            'price_per_month' => 1000,
            'capacity' => 1,
            'description' => null,
            'status' => 'available',
        ]);
    }

    private function createMeterForRoom(Room $room, string $type): Meter
    {
        return Meter::create([
            'room_id' => $room->id,
            'type' => $type,
            'meter_number' => 'MTR-' . $room->room_number . '-' . $type,
            'unit' => $type === 'electric' ? 'kWh' : 'Unit',
            'installed_at' => '2026-01-01',
            'is_active' => true,
            'notes' => 'notes',
            'rate_per_unit' => 1.50,
            'tax_rate' => 7.0,
        ]);
    }

    private function createMeterReading(Meter $meter, string $date, float $value, ?string $notes = null): MeterReading
    {
        $user = auth()->user();

        return MeterReading::create([
            'meter_id' => $meter->id,
            'reading_date' => $date,
            'reading_value' => $value,
            'notes' => $notes,
            'recorded_by' => $user?->id,
        ]);
    }
}

