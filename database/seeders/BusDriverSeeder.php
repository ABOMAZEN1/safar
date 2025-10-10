<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enum\UserTypeEnum;
use App\Models\BusDriver;
use App\Models\Role;
use App\Models\TravelCompany;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

final class BusDriverSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = collect([
            [
                'name' => 'أحمد محمد',
                'phone_number' => '0911111111',
            ],
            [
                'name' => 'محمد علي',
                'phone_number' => '0922222222',
            ],
            [
                'name' => 'علي حسن',
                'phone_number' => '0933333333',
            ],
            [
                'name' => 'حسن أحمد',
                'phone_number' => '0944444444',
            ],
        ]);

        $driverRole = Role::where('role_name', UserTypeEnum::BUS_DRIVER->value)->firstOrFail();
        $companies = TravelCompany::all();
        $now = now();

        // Check if these phone numbers already exist
        $existingUsers = User::whereIn('phone_number', $drivers->pluck('phone_number'))->get();
        $existingPhoneNumbers = $existingUsers->pluck('phone_number')->toArray();

        // Filter out existing phone numbers
        $newDrivers = $drivers->reject(fn(array $driver): bool => in_array($driver['phone_number'], $existingPhoneNumbers));

        if ($newDrivers->isEmpty()) {
            $this->command->info('All drivers already exist. Skipping driver creation.');
            return;
        }

        // Prepare users data for bulk insert (only for new users)
        $usersData = $newDrivers->map(fn($driver): array => [
            'name' => $driver['name'],
            'phone_number' => $driver['phone_number'],
            'password' => Hash::make('BusDriver@2024'),
            'type' => UserTypeEnum::BUS_DRIVER->value,
            'verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        // Bulk insert users
        if (!empty($usersData)) {
            User::insert($usersData);
        }

        // Get all inserted users (new + existing)
        $users = User::whereIn('phone_number', $drivers->pluck('phone_number'))->get();

        // Check for existing bus drivers
        $existingDriverUserIds = BusDriver::whereIn('user_id', $users->pluck('id'))->pluck('user_id')->toArray();

        // Prepare user roles data for bulk insert (only for users without roles)
        $userRolesData = $users->filter(function ($user) use ($driverRole): bool {
            // Check if user already has bus driver role
            $hasDriverRole = DB::table('user_roles')
                ->where('user_id', $user->id)
                ->whereIn('role_id', [$driverRole->id])
                ->exists();

            return !$hasDriverRole;
        })->map(fn($user): array => [
            'user_id' => $user->id,
            'role_id' => $driverRole->id,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        // Bulk insert user roles
        if (!empty($userRolesData)) {
            DB::table('user_roles')->insert($userRolesData);
        }

        // Prepare drivers data for bulk insert (only for users who aren't already drivers)
        $driversData = $users->filter(fn($user): bool => !in_array($user->id, $existingDriverUserIds))->map(fn($user, $index): array => [
            'user_id' => $user->id,
            'travel_company_id' => $companies[$index % count($companies)]->id,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        // Bulk insert drivers
        if (!empty($driversData)) {
            BusDriver::insert($driversData);
            $this->command->info('Created ' . count($driversData) . ' new bus drivers.');
        } else {
            $this->command->info('No new bus drivers to create.');
        }
    }
}
