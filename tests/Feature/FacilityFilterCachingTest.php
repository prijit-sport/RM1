<?php
 
namespace Tests\Feature;
 
use App\Models\Facility;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
 
/**
 * ป้องกัน regression ของบั๊ก: FacilityController::index() เคยใช้ cache key
 * ตายตัว ('facilities.all') ไม่ผันแปรตาม filter/page ทำให้ค้นหา/กรอง/เปลี่ยนหน้า
 * ไม่ทำงานจริงหลังจากมีคนเปิดหน้าแรกไปแล้วครั้งเดียว (cache hit คืนผลลัพธ์เดิม
 * โดยไม่สนใจ query parameter ใหม่เลย)
 */
class FacilityFilterCachingTest extends TestCase
{
    use RefreshDatabase;
 
    public function test_filtering_by_type_returns_different_results_after_unfiltered_request_was_cached(): void
    {
        $admin = $this->createUserWithRole('Admin');
 
        $room = Room::create([
            'room_number' => 'CACHE-101',
            'room_type' => 'Single',
            'price_per_month' => 1500,
            'capacity' => 1,
            'status' => 'occupied',
        ]);
 
        Facility::create([
            'room_id' => $room->id,
            'name' => 'เครื่องปรับอากาศ',
            'type' => 'aircon',
            'location' => 'ห้อง CACHE-101',
            'status' => 'active',
        ]);
 
        Facility::create([
            'room_id' => $room->id,
            'name' => 'เตียงนอน',
            'type' => 'furniture',
            'location' => 'ห้อง CACHE-101',
            'status' => 'active',
        ]);
 
        // Step 1: เปิดหน้าแบบไม่มี filter ก่อน (จุดที่เคยทำให้ cache ค้างเป็นผลลัพธ์รวม)
        $unfiltered = $this->actingAs($admin)->get(route('facilities.index'));
        $unfiltered->assertOk();
        $unfiltered->assertSeeText('เครื่องปรับอากาศ');
        $unfiltered->assertSeeText('เตียงนอน');
 
        // Step 2: กรองเฉพาะ type=aircon ทันทีหลังจากนั้น
        // ถ้าบั๊กยังไม่ได้แก้ ผลลัพธ์จะเหมือน step 1 เป๊ะ (มีเตียงนอนติดมาด้วย)
        $filtered = $this->actingAs($admin)->get(route('facilities.index', ['type' => 'aircon']));
        $filtered->assertOk();
        $filtered->assertSeeText('เครื่องปรับอากาศ');
        $filtered->assertDontSeeText('เตียงนอน');
    }
 
    public function test_search_returns_only_matching_results_after_unfiltered_request_was_cached(): void
    {
        $admin = $this->createUserWithRole('Admin');
 
        $room = Room::create([
            'room_number' => 'CACHE-102',
            'room_type' => 'Single',
            'price_per_month' => 1500,
            'capacity' => 1,
            'status' => 'occupied',
        ]);
 
        Facility::create([
            'room_id' => $room->id,
            'name' => 'ตู้เย็นมินิบาร์',
            'type' => 'appliance',
            'location' => 'ห้อง CACHE-102',
            'status' => 'active',
        ]);
 
        Facility::create([
            'room_id' => $room->id,
            'name' => 'โต๊ะเครื่องแป้ง',
            'type' => 'furniture',
            'location' => 'ห้อง CACHE-102',
            'status' => 'active',
        ]);
 
        $this->actingAs($admin)->get(route('facilities.index'))->assertOk();
 
        $searched = $this->actingAs($admin)->get(route('facilities.index', ['search' => 'ตู้เย็น']));
        $searched->assertOk();
        $searched->assertSeeText('ตู้เย็นมินิบาร์');
       $searched->assertSeeText('แสดง 1 ถึง 1');
    }
 
    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);
        return User::factory()->create(['role_id' => $role->id]);
    }
}
 