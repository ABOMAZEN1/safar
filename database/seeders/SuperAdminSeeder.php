<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enum\UserTypeEnum;
use App\Models\Role;
use App\Models\SuperAdmin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = collect([
            [
                'name' => 'Super Admin',
                'phone_number' => '0912345678',
            ],
        ]);

        $adminRole = Role::where('role_name', UserTypeEnum::SUPER_ADMIN->value)->firstOrFail();
        $now = now();

        // Prepare users data for bulk insert
        $usersData = $admins->map(fn ($admin): array => [
            'name' => $admin['name'],
            'phone_number' => $admin['phone_number'],
            'password' => Hash::make('StrongP@ss123'),
            'type' => UserTypeEnum::SUPER_ADMIN->value,
            'verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        // Bulk insert users
        User::insert($usersData);

        // Get the inserted users
        $users = User::whereIn('phone_number', $admins->pluck('phone_number'))->get();

        // Prepare user roles data for bulk insert
        $userRolesData = $users->map(fn ($user): array => [
            'user_id' => $user->id,
            'role_id' => $adminRole->id,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        // Bulk insert user roles
        DB::table('user_roles')->insert($userRolesData);

        // Prepare super admins data for bulk insert
        $superAdminsData = $users->map(fn ($user): array => [
            'user_id' => $user->id,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        // Bulk insert super admins
        SuperAdmin::insert($superAdminsData);
    }
}
