<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    private $password;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->password = Hash::make('12345678');

        $this->createSuperAdmin();
        $this->createAdmins();
        $this->createStaffs();
    }

    /**
     * Create super admin
     */
    private function createSuperAdmin(): void
    {
        $superAdmin = User::create([
            'owner_id' => 1,
            'uuid' => '8f7783ec56',
            'name' => 'Super Admin',
            'email' => 'superadmin@domain.com',
            'password' => '12345678',
            'status' => User::STATUS_ACTIVE,
            'created_at' => now(),
        ]);

        $superAdmin->assignRole(User::ROLE_SUPER_ADMIN);

        $superAdmin->settings()->create([
            'key' => 'is_dark_mode',
            'value' => false
        ]);

        $superAdmin->settings()->create([
            'key' => 'is_compact_sidebar',
            'value' => false
        ]);
    }

    private function createAdmins(): void
    {
        $admins = [];

        for ($i = 1; $i <= 1; $i++) {
            $admins[] = [
                'owner_id' => 1,
                'uuid' => getUuid(),
                'name' => 'Admin ' . $i,
                'email' => 'admin' . $i . '@domain.com',
                'password' => $this->password,
                'phone' => '12345679' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'status' => 'active',
                'created_at' => now(),
            ];
        }

        User::insert($admins);

        User::where('email', 'like', 'admin%@domain.com')->each(function ($user) {
            $user->assignRole(User::ROLE_ADMIN);
        });
    }

    private function createStaffs(): void
    {
        $admin1 = User::where('email', 'admin1@domain.com')->firstOrFail();

        $staffs = [];

        for ($i = 1; $i <= 3; $i++) {
            $staffs[] = [
                'owner_id' => $admin1->id,
                'uuid' => getUuid(),
                'name' => 'Staff ' . $i,
                'email' => 'staff' . $i . '@domain.com',
                'password' => $this->password,
                'phone' => '12345680' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'status' => 'active',
                'created_at' => now(),
            ];
        }

        User::insert($staffs);

        User::where('email', 'like', 'staff%@domain.com')->each(function ($user) {
            $user->assignRole(User::ROLE_STAFF);
        });
    }
}
