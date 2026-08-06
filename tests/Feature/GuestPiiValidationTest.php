<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ตรวจสอบว่า custom validation closure (hash lookup) ใน GuestController
 * ทำงานจริงผ่าน HTTP path หรือไม่ เมื่อมี guest ที่มี id_number/email ซ้ำอยู่แล้ว
 *
 * Background:
 * - Manual test (b) ใช้ tinker เรียก Guest::create() โดยตรง ซึ่ง bypass controller
 *   validation → ไปชน unique index บน id_number_hash → ได้ UniqueConstraintViolationException
 * - Test นี้ยืนยันว่าเมื่อผ่าน HTTP store() จริง ๆ custom closure จะทำงานและ
 *   คืน ValidationException (user-friendly) ไม่ใช่ raw DB exception
 */
class GuestPiiValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_duplicate_id_number_returns_validation_error_not_db_exception(): void
    {
        $user = $this->createUserWithRole('Admin');
        $this->actingAs($user);

        // สร้าง guest ตัวแรกก่อน (มี id_number ซ้ำจำลอง)
        $existing = Guest::create([
            'first_name' => 'Existing',
            'last_name' => 'Person',
            'email' => 'existing@example.com',
            'phone' => '0811111111',
            'id_number' => 'ID-DUP-001',
        ]);
        $this->assertNotNull($existing->id_number_hash);

        // ส่ง POST ผ่าน HTTP store() ด้วย id_number ซ้ำ
        $response = $this->from(route('guests.create'))
            ->post(route('guests.store'), [
                'first_name' => 'New',
                'last_name' => 'Person',
                'email' => 'new@example.com',
                'phone' => '0822222222',
                'id_number' => 'ID-DUP-001',
            ]);

        // ต้องได้ redirect กลับไปหน้า create พร้อม validation error (ไม่ใช่ raw DB exception)
        $response->assertRedirect(route('guests.create'));
        $response->assertSessionHasErrors('id_number');
        $response->assertSessionHasErrors(['id_number' => 'The id number has already been taken.']);

        // ต้องไม่มี record ใหม่ถูกสร้าง (สำคัญ: ไม่ใช่ raw DB exception)
        $this->assertSame(1, Guest::count());
    }

    public function test_store_duplicate_email_returns_validation_error_not_db_exception(): void
    {
        $user = $this->createUserWithRole('Admin');
        $this->actingAs($user);

        $existing = Guest::create([
            'first_name' => 'Existing',
            'last_name' => 'Person',
            'email' => 'dup@example.com',
            'phone' => '0811111111',
            'id_number' => 'ID-DUP-002',
        ]);
        $this->assertNotNull($existing->email_hash);

        $response = $this->from(route('guests.create'))
            ->post(route('guests.store'), [
                'first_name' => 'New',
                'last_name' => 'Person',
                'email' => 'dup@example.com',
                'phone' => '0822222222',
                'id_number' => 'ID-UNIQUE-200',
            ]);

        $response->assertRedirect(route('guests.create'));
        $response->assertSessionHasErrors('email');
        $response->assertSessionHasErrors(['email' => 'The email has already been taken.']);
        $this->assertSame(1, Guest::count());
    }

    public function test_store_unique_id_number_creates_guest_successfully(): void
    {
        $user = $this->createUserWithRole('Admin');
        $this->actingAs($user);

        Guest::create([
            'first_name' => 'Existing',
            'last_name' => 'Person',
            'email' => 'existing@example.com',
            'phone' => '0811111111',
            'id_number' => 'ID-EXISTING-1',
        ]);

        $response = $this->post(route('guests.store'), [
            'first_name' => 'New',
            'last_name' => 'Person',
            'email' => 'new@example.com',
            'phone' => '0822222222',
            'id_number' => 'ID-NEW-2',
        ]);

        $response->assertRedirect(route('guests.index'));
        $this->assertSame(2, Guest::count());
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }
}
