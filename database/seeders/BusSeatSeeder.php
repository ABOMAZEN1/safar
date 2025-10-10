<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BusSeat;
use App\Models\BusTrip;
use Illuminate\Database\Seeder;
use RuntimeException;

final class BusSeatSeeder extends Seeder
{
    public function run(): void
    {
        $trips = BusTrip::all();

        if ($trips->isEmpty()) {
            throw new RuntimeException('No bus trips found. Please run BusTripSeeder first.');
        }

        $now = now();
        $seats = [];

        foreach ($trips as $trip) {
            for ($seatNumber = 1; $seatNumber <= $trip->number_of_seats; $seatNumber++) {
                $seats[] = [
                    'bus_trip_id' => $trip->id,
                    'seat_number' => $seatNumber,
                    'is_reserved' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                // Insert in smaller chunks to avoid memory issues
                if (count($seats) >= 1000) {
                    BusSeat::insert($seats);
                    $seats = [];
                }
            }
        }

        // Insert any remaining seats
        if ($seats !== []) {
            BusSeat::insert($seats);
        }
    }
}
