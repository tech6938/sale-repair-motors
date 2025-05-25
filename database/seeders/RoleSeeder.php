<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => User::ROLE_SUPER_ADMIN, 'guard_name' => 'web']);
        Role::create(['name' => User::ROLE_ADMIN, 'guard_name' => 'web']);
        Role::create(['name' => User::ROLE_STAFF, 'guard_name' => 'web']);
    }
}
