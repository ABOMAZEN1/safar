<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enum\UserTypeEnum;
use App\Models\Role;
use Illuminate\Database\Seeder;

final class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'role_name' => UserTypeEnum::SUPER_ADMIN->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => UserTypeEnum::TRAVEL_COMPANY->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => UserTypeEnum::BUS_DRIVER->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => UserTypeEnum::CUSTOMER->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Make seeding idempotent to avoid duplicate key errors on reruns
        // Uses role_name as the unique key and refreshes updated_at
        \App\Models\Role::upsert(
            $roles,
            ['role_name'],     // unique by role_name
            ['updated_at']     // columns to update on conflict
        );
    }
}
