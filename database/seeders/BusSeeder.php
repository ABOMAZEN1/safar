<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\BusType;
use App\Models\TravelCompany;
use Illuminate\Database\Seeder;

final class BusSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $busTypes = BusType::all();
        $companies = TravelCompany::all();

        $buses = [];

        // Create 50 buses with different types and companies
        for ($i = 0; $i < 50; $i++) {
            $buses[] = [
                'bus_type_id' => $busTypes->random()->id,
                'travel_company_id' => $companies->random()->id,
                'capacity' => random_int(30, 50),
                'details' => 'Bus ' . ($i + 1),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Bulk insert buses in chunks
        foreach (array_chunk($buses, 10) as $chunk) {
            Bus::insert($chunk);
        }
    }
}
