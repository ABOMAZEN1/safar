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

        Role::insert($roles);
    }
}
