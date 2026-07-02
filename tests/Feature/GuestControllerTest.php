<?php
 
namespace Tests\Feature;
 
use App\Models\Guest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
 
class GuestControllerTest extends TestCase
{
    use RefreshDatabase;
 
    public function test_index_returns_view_for_authenticated_user(): void
    {
        $this->actingAs($this->createUserWithRole('User'));
 
        Guest::create([
            'first_name' => 'Alice',
            'last_name'  => 'Johnson',
            'email'      => 'alice@example.com',
            'phone'      => '0811111111',
            'address'    => 'Bangkok',
            'city'       => 'Bangkok',
            'country'    => 'Thailand',
            'id_number'  => 'ID-1001',
        ]);
 
        $response = $this->get(route('guests.index'));
 
        $response->assertOk();
        $response->assertViewIs('guests.index');
        $response->assertViewHas('guests');
        $response->assertSeeText('จัดการผู้เช่า');
    }
 
    public function test_store_rejects_empty_payload_and_creates_no_record(): void
    {
        // ✅ โปรเจคนี้ใช้ bootstrap/app.php Throwable handler
        // validation fail → ได้ 500 จาก handler (ไม่ใช่ redirect ปกติ)
        // ดังนั้น assert ว่า "ไม่ได้ 200" และ "ไม่มี record ถูกสร้าง"
        $user = $this->createUserWithRole('Admin');
 
        $response = $this->actingAs($user)
            ->from(route('guests.create'))
            ->post(route('guests.store'), []);
 
        // ✅ ยืนยันว่าไม่ได้ 200 OK (validation ทำงาน)
        $response->assertRedirect(route('guests.create'));

        $response->assertSessionHasErrors();

 
        // ✅ ยืนยันว่าไม่มี Guest ถูกสร้าง (สำคัญที่สุด)
        $this->assertSame(0, Guest::count());
    }
 
    public function test_store_creates_guest_with_valid_data(): void
    {
        $user = $this->createUserWithRole('Admin');
 
        $response = $this->actingAs($user)
            ->post(route('guests.store'), [
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'email'      => 'john@example.com',
                'phone'      => '0811111111',
                'id_number'  => 'ID-TEST-001',
            ]);
 
        // ✅ ส่งข้อมูลถูกต้อง ต้อง redirect ไปหน้า index
        $response->assertRedirect(route('guests.index'));
 
        // ✅ ยืนยันว่ามี Guest ถูกสร้าง 1 record
        $this->assertSame(1, Guest::count());
    }
 
    public function test_show_displays_guest_details_for_existing_guest(): void
    {
        $this->actingAs($this->createUserWithRole('User'));
 
        $guest = $this->createGuest();
 
        $response = $this->get(route('guests.show', $guest));
 
        $response->assertOk();
        $response->assertViewIs('guests.show');
        $response->assertSeeText($guest->first_name);
        $response->assertSeeText($guest->last_name);
    }
 
    public function test_index_redirects_to_login_for_unauthenticated_user(): void
    {
        $response = $this->get(route('guests.index'));
 
        $response->assertRedirect(route('login'));
    }
 
    private function createGuest(): Guest
    {
        return Guest::create([
            'first_name' => 'Charlie',
            'last_name'  => 'Brown',
            'email'      => 'charlie@example.com',
            'phone'      => '0822222222',
            'address'    => 'Chiang Mai',
            'city'       => 'Chiang Mai',
            'country'    => 'Thailand',
            'id_number'  => 'ID-2001',
        ]);
    }
 
    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);
 
        return User::factory()->create(['role_id' => $role->id]);
    }
}
 