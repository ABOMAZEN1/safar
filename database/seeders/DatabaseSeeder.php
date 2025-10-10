<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CitySeeder::class,
            SuperAdminSeeder::class,
            BusTypeSeeder::class,
            TravelCompanySeeder::class,
            TravelCompanyCommissionSeeder::class,
            BusSeeder::class,
            BusDriverSeeder::class,
            BusTripSeeder::class,
            BusSeatSeeder::class,
            AppSettingSeeder::class,
            CustomerSeeder::class,
            TermsAndConditionsSeeder::class,
            PrivacyPolicySeeder::class,
            BusTripBookingSeeder::class,
            TravelCompanionSeeder::class,
        ]);
    }
}
