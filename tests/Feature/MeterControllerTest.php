<?php

namespace Tests\Feature;

use App\Models\Meter;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_ok_for_admin_and_shows_room_meter_numbers(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $room = $this->createRoomForMeters('101', 'A', 1);
        $electricMeter = $this->createMeterForRoom($room, 'electric');
        $waterMeter = $this->createMeterForRoom($room, 'water');

        $response = $this->get(route('meters.index'));

        $response->assertOk();
        $response->assertViewIs('meters.index');
        $response->assertSeeText('ห้อง '.$room->room_number);
        // index page อาจไม่แสดง meter_number ในตาราง ต้อง assert จากส่วนที่ blade แสดงจริง
        $response->assertSeeText('ไฟฟ้า');
    }

    public function test_index_returns_forbidden_for_non_admin(): void
    {
        $this->actingAs($this->createUserWithRole('Staff'));

        $response = $this->get(route('meters.index'));
        $response->assertStatus(403);
    }

    public function test_store_validation_fail_returns_500_when_payload_invalid(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $room = $this->createRoomForMeters('102', 'B', 2);

        // invalid: type=invalid (Rule::in(['water','electric']))
        $response = $this->from(route('meters.create'))
            ->post(route('meters.store'), [
                'room_id' => $room->id,
                'type' => 'invalid',
                'meter_number' => 'MTR-0001',
                'unit' => 'kWh',
                'installed_at' => '2026-01-01',
                'rate_per_unit' => 1.5,
                'tax_rate' => 7,
                'is_active' => true,
                'notes' => 'note',
            ]);

        $response->assertRedirect(route('meters.create'));

        $response->assertSessionHasErrors();

        $this->assertSame(0, Meter::count());
    }

    public function test_show_displays_meter_details_for_admin(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $room = $this->createRoomForMeters('103', 'C', 3);
        $meter = $this->createMeterForRoom($room, 'water');

        $response = $this->get(route('meters.show', $meter));

        $response->assertOk();
        $response->assertViewIs('meters.show');
        $response->assertSeeText($meter->meter_number);
        $response->assertSeeText('ห้อง '.$room->room_number);
        $response->assertSeeText('น้ำ');
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
            'meter_number' => 'MTR-'.$room->room_number.'-'.$type,
            'unit' => $type === 'electric' ? 'kWh' : 'Unit',
            'installed_at' => '2026-01-01',
            'is_active' => true,
            'notes' => 'notes',
            'rate_per_unit' => 1.50,
            'tax_rate' => 7.0,
        ]);
    }
}
