<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AuthorizationLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_only_denied_when_role_missing(): void
    {
        // user without any role (role_id = null)
        $user = User::factory()->create(['role_id' => null]);

        Event::fake();

        $this->actingAs($user);

        $this->get(route('roles.index'))->assertStatus(403);

        Event::assertDispatched(MessageLogged::class, function (MessageLogged $event) use ($user) {
            return $event->level === 'warning'
                && $event->message === 'Authorization denied'
                && $event->context['route'] === 'roles.index'
                && $event->context['user_id'] === $user->id
                && $event->context['role'] === 'none'
                && $event->context['reason'] === 'missing_role';
        });
    }

    public function test_admin_only_denied_when_not_admin(): void
    {
        // user with a non-admin role
        $role = Role::firstOrCreate(['name' => 'Staff'], ['description' => 'Staff user']);
        $user = User::factory()->create(['role_id' => $role->id]);

        Event::fake();

        $this->actingAs($user);

        $this->get(route('roles.index'))->assertStatus(403);

        Event::assertDispatched(MessageLogged::class, function (MessageLogged $event) use ($user) {
            return $event->level === 'warning'
                && $event->message === 'Authorization denied'
                && $event->context['route'] === 'roles.index'
                && $event->context['user_id'] === $user->id
                && $event->context['role'] === 'Staff'
                && $event->context['reason'] === 'not_admin';
        });
    }
}

