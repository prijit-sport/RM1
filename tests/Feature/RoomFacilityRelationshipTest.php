<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ปิดช่องว่าง test coverage ของความสัมพันธ์ Room <-> Facility ที่ยังไม่เคย
 * ทดสอบแบบเต็มรูปแบบมาก่อน ครอบคลุม:
 * - Soft delete ห้องพักไม่กระทบ room_id ของ Facility เลย (เพราะ Room ใช้ SoftDeletes)
 * - Force delete ห้องพัก (ลบแถวจริง) ทำให้ room_id ของ Facility เป็น null ตาม nullOnDelete
 * - หน้าแสดงรายละเอียด Facility ยังทำงานได้ปกติแม้ room_id เป็น null
 * - การแก้ไข (update) และลบ (destroy) Facility ทำงานถูกต้อง
 */
class RoomFacilityRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_soft_deleting_room_does_not_remove_facility_and_keeps_room_id(): void
    {
        // Room ใช้ SoftDeletes: delete() แค่ตั้ง deleted_at ไม่ได้ลบแถวออกจริง
        // foreign key nullOnDelete จะทำงานก็ต่อเมื่อลบแถวจริง (forceDelete)
        // ดังนั้น room_id ของ Facility ต้องยังคงเดิมหลัง soft delete
        $admin = $this->createUserWithRole('Admin');
        $this->actingAs($admin);

        $room = Room::create([
            'room_number' => 'RF-101',
            'room_type' => 'Single',
            'price_per_month' => 1500,
            'capacity' => 1,
            'status' => 'available',
        ]);

        $facility = Facility::create([
            'room_id' => $room->id,
            'name' => 'เตียงนอน RF-101',
            'type' => 'bed',
            'location' => 'ห้อง RF-101',
            'status' => 'good',
        ]);

        $room->delete();

        $facility->refresh();

        $this->assertSame($room->id, $facility->room_id, 'Soft delete ไม่ควรกระทบ room_id ของ Facility เลย');
        $this->assertDatabaseHas('facilities', ['id' => $facility->id]);
        $this->assertSoftDeleted('rooms', ['id' => $room->id]);
    }

    public function test_force_deleting_room_sets_facility_room_id_to_null(): void
    {
        // เฉพาะตอน forceDelete (ลบแถวออกจากฐานข้อมูลจริง) เท่านั้นที่ nullOnDelete จะทำงาน
        $admin = $this->createUserWithRole('Admin');
        $this->actingAs($admin);

        $room = Room::create([
            'room_number' => 'RF-105',
            'room_type' => 'Single',
            'price_per_month' => 1500,
            'capacity' => 1,
            'status' => 'available',
        ]);

        $facility = Facility::create([
            'room_id' => $room->id,
            'name' => 'เตียงนอน RF-105',
            'type' => 'bed',
            'location' => 'ห้อง RF-105',
            'status' => 'good',
        ]);

        $room->forceDelete();

        $facility->refresh();

        $this->assertNull($facility->room_id, 'forceDelete ต้องทำให้ room_id ของ Facility เป็น null ตาม nullOnDelete');
        $this->assertDatabaseHas('facilities', ['id' => $facility->id]);
    }

    public function test_facility_show_page_works_correctly_when_room_is_null(): void
    {
        $admin = $this->createUserWithRole('Admin');
        $this->actingAs($admin);

        $facility = Facility::create([
            'room_id' => null,
            'name' => 'อุปกรณ์ส่วนกลาง',
            'type' => 'furniture',
            'location' => 'คลังเก็บของ',
            'status' => 'good',
        ]);

        $response = $this->get(route('facilities.show', $facility));

        $response->assertOk();
        $response->assertSeeText('อุปกรณ์ส่วนกลาง');
    }

    public function test_update_facility_changes_data_correctly(): void
    {
        $admin = $this->createUserWithRole('Admin');
        $this->actingAs($admin);

        $room = Room::create([
            'room_number' => 'RF-102',
            'room_type' => 'Single',
            'price_per_month' => 1500,
            'capacity' => 1,
            'status' => 'available',
        ]);

        $facility = Facility::create([
            'room_id' => $room->id,
            'name' => 'เตียงนอนเก่า',
            'type' => 'bed',
            'location' => 'ห้อง RF-102',
            'status' => 'good',
        ]);

        $response = $this->put(route('facilities.update', $facility), [
            'room_id' => $room->id,
            'name' => 'เตียงนอนใหม่',
            'type' => 'bed',
            'location' => 'ห้อง RF-102',
            'status' => 'needs_repair',
        ]);

        $response->assertRedirect(route('facilities.show', $facility));

        $facility->refresh();
        $this->assertSame('เตียงนอนใหม่', $facility->name);
        $this->assertSame('needs_repair', $facility->status);
    }

    public function test_destroy_facility_removes_it_from_database(): void
    {
        $admin = $this->createUserWithRole('Admin');
        $this->actingAs($admin);

        $room = Room::create([
            'room_number' => 'RF-103',
            'room_type' => 'Single',
            'price_per_month' => 1500,
            'capacity' => 1,
            'status' => 'available',
        ]);

        $facility = Facility::create([
            'room_id' => $room->id,
            'name' => 'จะถูกลบ',
            'type' => 'bed',
            'location' => 'ห้อง RF-103',
            'status' => 'good',
        ]);

        $response = $this->delete(route('facilities.destroy', $facility));

        $response->assertRedirect(route('facilities.index'));
        $this->assertDatabaseMissing('facilities', ['id' => $facility->id]);
    }

    public function test_non_admin_cannot_update_facility(): void
    {
        $user = $this->createUserWithRole('User');
        $this->actingAs($user);

        $room = Room::create([
            'room_number' => 'RF-104',
            'room_type' => 'Single',
            'price_per_month' => 1500,
            'capacity' => 1,
            'status' => 'available',
        ]);

        $facility = Facility::create([
            'room_id' => $room->id,
            'name' => 'ป้องกันสิทธิ์',
            'type' => 'bed',
            'location' => 'ห้อง RF-104',
            'status' => 'good',
        ]);

        $response = $this->put(route('facilities.update', $facility), [
            'room_id' => $room->id,
            'name' => 'พยายามแก้',
            'type' => 'bed',
            'location' => 'ห้อง RF-104',
            'status' => 'good',
        ]);

        $response->assertStatus(403);
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);
        return User::factory()->create(['role_id' => $role->id]);
    }
}