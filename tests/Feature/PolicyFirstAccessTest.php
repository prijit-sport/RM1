<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class PolicyFirstAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_is_denied_by_role_policy(): void
    {
        $user = $this->createUserWithRole('User');

        $this->assertTrue(Gate::forUser($user)->denies('viewAny', Role::class));
    }

    public function test_admin_is_allowed_by_role_policy(): void
    {
        $user = $this->createUserWithRole('Admin');

        $this->assertTrue(Gate::forUser($user)->allows('viewAny', Role::class));
    }

    public function test_regular_user_cannot_access_roles_index_route(): void
    {
        $user = $this->createUserWithRole('User');

        $this->actingAs($user);

        $this->get(route('roles.index'))->assertForbidden();
    }

    public function test_admin_can_access_roles_index_route(): void
    {
        $user = $this->createUserWithRole('Admin');

        $this->actingAs($user);

        $this->get(route('roles.index'))->assertOk();
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }
}
