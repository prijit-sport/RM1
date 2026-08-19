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
        $this->actingAs(User::factory()->create(['role_id' => null]));

        Guest::create([
            'first_name' => 'Alice',
            'last_name' => 'Johnson',
            'email' => 'alice@example.com',
            'phone' => '0811111111',
            'address' => 'Bangkok',
            'city' => 'Bangkok',
            'country' => 'Thailand',
            'id_number' => 'ID-1001',
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
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'phone' => '0811111111',
                'id_number' => 'ID-TEST-001',
            ]);

        // ✅ ส่งข้อมูลถูกต้อง ต้อง redirect ไปหน้า index
        $response->assertRedirect(route('guests.index'));

        // ✅ ยืนยันว่ามี Guest ถูกสร้าง 1 record
        $this->assertSame(1, Guest::count());
    }

    public function test_show_displays_guest_details_for_existing_guest(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => null]));

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

    public function test_index_search_by_partial_name_returns_matching_guests(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => null]));

        // Create multiple guests with different names
        Guest::create([
            'first_name' => 'Emanuel',
            'last_name' => 'Garcia',
            'email' => 'emanuel@example.com',
            'phone' => '0811111111',
            'id_number' => '1234567890',
        ]);

        Guest::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '0822222222',
            'id_number' => '1234567891',
        ]);

        // Search by partial first name
        $response = $this->get(route('guests.index', ['search' => 'emanu']));

        $response->assertOk();
        $response->assertSeeText('Emanuel');
        $response->assertDontSeeText('John');
    }

    public function test_index_search_by_full_email_exact_match_returns_result(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => null]));

        $guest = Guest::create([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'email' => 'alice@example.com',
            'phone' => '0811111111',
            'id_number' => '1234567892',
        ]);

        // Search by complete email (exact match)
        $response = $this->get(route('guests.index', ['search' => 'alice@example.com']));

        $response->assertOk();
        $response->assertSeeText('Alice');
    }

    public function test_index_search_by_partial_email_returns_no_results(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => null]));

        Guest::create([
            'first_name' => 'Bob',
            'last_name' => 'Wilson',
            'email' => 'bob@example.com',
            'phone' => '0811111111',
            'id_number' => '1234567893',
        ]);

        // Search by partial email string (contains @ but incomplete/wrong)
        // Since it contains @, system treats as PII exact-match lookup
        // This should NOT match partial email searches anymore
        $response = $this->get(route('guests.index', ['search' => 'bob@exam']));

        $response->assertOk();
        // Should NOT find guest when searching partial email
        $response->assertDontSeeText('Wilson');
    }

    public function test_index_search_by_id_number_exact_match_returns_result(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => null]));

        $guest = Guest::create([
            'first_name' => 'Charlie',
            'last_name' => 'Brown',
            'email' => 'charlie@example.com',
            'phone' => '0811111111',
            'id_number' => '1234567894',
        ]);

        // Search by complete ID number (exact match)
        $response = $this->get(route('guests.index', ['search' => '1234567894']));

        $response->assertOk();
        $response->assertSeeText('Charlie');
    }

    public function test_index_search_by_partial_id_number_returns_no_results(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => null]));

        Guest::create([
            'first_name' => 'David',
            'last_name' => 'Lee',
            'email' => 'david@example.com',
            'phone' => '0811111111',
            'id_number' => '1234567895',
        ]);

        // Search by partial ID number (NOT supported - must be exact or >= 10 digits)
        $response = $this->get(route('guests.index', ['search' => '12345']));

        $response->assertOk();
        // Partial ID number should not match
        $response->assertDontSeeText('David');
    }

    public function test_index_search_by_full_phone_number_exact_match_returns_result(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => null]));

        Guest::create([
            'first_name' => 'Phone',
            'last_name' => 'Owner',
            'email' => 'phoneowner@example.com',
            'phone' => '0811111111',
            'id_number' => '9876543210',
        ]);

        $response = $this->get(route('guests.index', ['search' => '0811111111']));

        $response->assertOk();
        $response->assertSeeText('Phone');
    }

    public function test_index_without_search_paginates_at_sql_level(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => null]));

        // Create 15 guests to test pagination
        for ($i = 1; $i <= 15; $i++) {
            Guest::create([
                'first_name' => "Guest{$i}",
                'last_name' => "Last{$i}",
                'email' => "guest{$i}@example.com",
                'phone' => "081" . str_pad($i, 8, '0', STR_PAD_LEFT),
                'id_number' => (1234567890 + $i),
            ]);
        }

        // First page should have 10 guests
        $response = $this->get(route('guests.index'));
        $response->assertOk();
        $guests = $response->viewData('guests');
        $this->assertCount(10, $guests);
        $this->assertTrue($guests->hasPages());

        // Second page should have 5 guests
        $response = $this->get(route('guests.index', ['page' => 2]));
        $response->assertOk();
        $guests = $response->viewData('guests');
        $this->assertCount(5, $guests);
    }

    private function createGuest(): Guest
    {
        return Guest::create([
            'first_name' => 'Charlie',
            'last_name' => 'Brown',
            'email' => 'charlie@example.com',
            'phone' => '0822222222',
            'address' => 'Chiang Mai',
            'city' => 'Chiang Mai',
            'country' => 'Thailand',
            'id_number' => 'ID-2001',
        ]);
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }
}

