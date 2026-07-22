<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'Admin', 'description' => 'Administrator - Full access']);
        Role::create(['name' => 'Manager', 'description' => 'Manager - Can manage resources']);
        Role::create(['name' => 'Staff', 'description' => 'Staff - Can handle day-to-day operations']);
        Role::create(['name' => 'User', 'description' => 'Regular User - Limited access']);
    }
}

