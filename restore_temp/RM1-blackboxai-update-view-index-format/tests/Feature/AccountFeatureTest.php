<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_accessible_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('name="name"', false)
            ->assertSee($user->email);
    }

    public function test_profile_can_be_updated(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);
        $this->actingAs($user);

        $this->put(route('profile.update'), [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertSame('New Name', $user->name);
        $this->assertSame('new@example.com', $user->email);
    }

    public function test_settings_page_and_update_work(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('settings.edit'))
            ->assertOk()
            ->assertSee('items_per_page', false);

        $this->put(route('settings.update'), [
            'locale' => 'en',
            'items_per_page' => 50,
            'compact_mode' => '1',
        ])->assertRedirect(route('settings.edit'));

        $this->assertSame('en', session('settings.locale'));
        $this->assertSame(50, session('settings.items_per_page'));
        $this->assertTrue((bool) session('settings.compact_mode'));
    }
}
