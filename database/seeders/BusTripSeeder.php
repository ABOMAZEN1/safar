<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\BusDriver;
use App\Models\BusTrip;
use App\Models\City;
use App\Models\TravelCompany;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use RuntimeException;

final class BusTripSeeder extends Seeder
{
    public function run(): void
    {
        $buses = Bus::with('travelCompany')->get();

        if ($buses->isEmpty()) {
            throw new RuntimeException('No buses found. Please run BusSeeder first.');
        }

        $drivers = BusDriver::with('travelCompany')->get();

        if ($drivers->isEmpty()) {
            throw new RuntimeException('No drivers found. Please run BusDriverSeeder first.');
        }

        $cities = City::get();

        if ($cities->count() < 2) {
            throw new RuntimeException('Not enough active cities found. Please run CitySeeder first.');
        }

        $now = now();
        $trips = [];

        // Create a fixed trip for testing with validated company relationships
        $company = TravelCompany::find(1);
        if ($company) {
            $bus = $buses->where('travel_company_id', $company->id)->firstOrFail();
            $driver = $drivers->where('travel_company_id', $company->id)->firstOrFail();

            if ($bus && $driver) {
                // Ensure number_of_seats doesn't exceed bus capacity
                $seatCount = min(50, $bus->capacity);

                $trips[] = [
                    'from_city_id' => 1, // Specific city ID for testing
                    'to_city_id' => 2,   // Specific city ID for testing
                    'bus_id' => $bus->id,
                    'bus_driver_id' => $driver->id,
                    'travel_company_id' => $company->id,
                    'departure_datetime' => now()->addDays(1)->startOfDay(), // Tomorrow
                    'return_datetime' => null, // One-way trip
                    'duration_of_departure_trip' => 3, // 3 hours
                    'duration_of_return_trip' => null,
                    'trip_type' => 'one_way',
                    'number_of_seats' => $seatCount,
                    'remaining_seats' => $seatCount - 2, // 2 seats taken
                    'ticket_price' => 50.00, // Fixed price for testing
                    'image' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            } else {
                $this->command->warn('Could not create test trip: bus or driver not found for company ID 1');
            }
        }

        // Create additional random trips with proper company relationships
        $companies = TravelCompany::all();
        $tripCount = 0;
        $maxTrips = 100;
        $maxAttempts = 200; // Limit attempts to avoid infinite loops
        $attempts = 0;

        while ($tripCount < $maxTrips && $attempts < $maxAttempts) {
            $attempts++;

            // First select a company that has both buses and drivers
            $company = $companies->random();
            $companyBuses = $buses->where('travel_company_id', $company->id);
            $companyDrivers = $drivers->where('travel_company_id', $company->id);
            // Skip if company has no buses or drivers
            if ($companyBuses->isEmpty()) {
                continue;
            }
            if ($companyDrivers->isEmpty()) {
                continue;
            }

            // Now select a bus and driver from the same company
            $bus = $companyBuses->random();
            $driver = $companyDrivers->random();

            // Select random cities
            $fromCity = $cities->random();
            $toCity = $cities->except($fromCity->id)->random();

            $departureDateTime = now()->addDays(random_int(1, 30));

            // Random duration between 2 to 8 hours
            $duration = random_int(2, 8);

            // Ensure number_of_seats doesn't exceed bus capacity
            $seatCount = min($bus->capacity, random_int($bus->capacity - 10, $bus->capacity));

            $trips[] = [
                'from_city_id' => $fromCity->id,
                'to_city_id' => $toCity->id,
                'bus_id' => $bus->id,
                'bus_driver_id' => $driver->id,
                'travel_company_id' => $company->id,
                'departure_datetime' => $departureDateTime,
                'return_datetime' => null, // One-way trip
                'duration_of_departure_trip' => $duration,
                'duration_of_return_trip' => null, // One-way trip
                'trip_type' => 'one_way',
                'number_of_seats' => $seatCount,
                'remaining_seats' => $seatCount,
                'ticket_price' => random_int(2000, 10000) / 100, // Random price between 20 and 100
                'image' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $tripCount++;
        }

        if ($tripCount < $maxTrips) {
            $this->command->warn(sprintf('Only created %d trips out of %d requested due to company/bus/driver constraints', $tripCount, $maxTrips));
        }

        // Bulk insert trips in chunks to avoid memory issues
        foreach (array_chunk($trips, 20) as $chunk) {
            BusTrip::insert($chunk);
        }

        $this->command->info('Successfully created ' . count($trips) . ' bus trips.');
    }
}
