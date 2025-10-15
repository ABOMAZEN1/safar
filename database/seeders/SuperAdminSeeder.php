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
        $now = now();

        // Allow overriding credentials from environment
        $phone = env('ADMIN_PHONE', '0999999999');
        $password = env('ADMIN_PASSWORD', 'Admin@12345');
        $name = env('ADMIN_NAME', 'Super Admin');

        $adminRole = Role::where('role_name', UserTypeEnum::SUPER_ADMIN->value)->firstOrFail();

        // Create or update the super admin user idempotently
        /** @var User $user */
        $user = User::updateOrCreate(
            ['phone_number' => $phone],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'type' => UserTypeEnum::SUPER_ADMIN->value,
                'verified_at' => $now,
                'updated_at' => $now,
            ]
        );

        // Attach role if missing
        DB::table('user_roles')->updateOrInsert(
            ['user_id' => $user->id, 'role_id' => $adminRole->id],
            ['created_at' => $now, 'updated_at' => $now]
        );

        // Ensure a record exists in super_admins table
        SuperAdmin::updateOrCreate(
            ['user_id' => $user->id],
            ['updated_at' => $now, 'created_at' => $user->created_at ?? $now]
        );
    }
}
