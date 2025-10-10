<?php

declare(strict_types=1);

namespace Database\Seeders;

use Exception;
use App\Enum\BookingStatusEnum;
use App\Models\BusTripBooking;
use App\Models\TravelCompanion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class TravelCompanionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $bookings = BusTripBooking::with(['busTrip', 'busTrip.bus', 'busTrip.bus.busType', 'customer'])
                ->where('reserved_seat_count', '>', 1)
                ->where('booking_status', '!=', BookingStatusEnum::CANCELED->value)
                ->get();

            if ($bookings->isEmpty()) {
                $this->command->info('No valid bookings found with more than 1 seat. Please run BusTripBookingSeeder first.');
                return;
            }

            $this->command->info('Found ' . $bookings->count() . ' valid bookings for adding companions.');
            $this->command->info('Creating travel companions...');

            $successCount = 0;
            $errorCount = 0;

            foreach ($bookings as $booking) {
                // For each booking, create companions equal to one less than the seat count
                // (assuming one seat is for the customer)
                $companionCount = $booking->reserved_seat_count - 1;

                for ($i = 0; $i < $companionCount; $i++) {
                    try {
                        // Create a new companion directly
                        $companion = new TravelCompanion([
                            'bus_trip_booking_id' => $booking->id,
                            'companion_name' => fake()->name(),
                        ]);

                        $companion->save();
                        $successCount++;
                    } catch (Exception $e) {
                        $this->command->error(sprintf('Error creating companion for booking %d: ', $booking->id) . $e->getMessage());
                        $errorCount++;
                    }
                }
            }

            $this->command->info(sprintf('Successfully created %d travel companions. Encountered %d errors.', $successCount, $errorCount));
        });
    }
}
