<?php

declare(strict_types=1);

namespace Database\Seeders;

use Exception;
use Illuminate\Support\Collection;
use App\Models\BusTrip;
use App\Models\Customer;
use App\Models\BusTripBooking;
use App\Enum\BusTripStatusEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class BusTripBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            // Get valid bus trips where driver belongs to the same company
            $busTrips = BusTrip::with([
                'bus',
                'bus.busType',
                'busDriver',
                'busDriver.travelCompany',
                'travelCompany'
            ])
                ->where('remaining_seats', '>', 0)
                ->get()
                ->filter(function ($busTrip): bool {
                    // Ensure busDriver exists and belongs to the same travel company
                    if (!$busTrip->busDriver || !$busTrip->travelCompany) {
                        return false;
                    }

                    return $busTrip->busDriver->travel_company_id === $busTrip->travel_company_id;
                });

            if ($busTrips->isEmpty()) {
                $this->command->info('No valid bus trips found. Please ensure you have bus trips with drivers belonging to the same company.');
                return;
            }

            $this->command->info('Found ' . $busTrips->count() . ' valid bus trips with correct driver-company relationships.');

            $customers = Customer::all();
            if ($customers->isEmpty()) {
                $this->command->info('No customers found. Please run CustomerSeeder first.');
                return;
            }

            $this->command->info('Creating bus trip bookings...');

            $this->createSampleBookings($busTrips, $customers, 15, BusTripStatusEnum::COMPLETED->value);

            $this->createSampleBookings($busTrips, $customers, 20, BusTripStatusEnum::ACTIVE->value);

            $this->createSampleBookings($busTrips, $customers, 5, BusTripStatusEnum::CANCELED->value);

            $this->command->info('Bus trip bookings created successfully!');
        });
    }

    /**
     * Create sample bookings with the given status.
     *
     * @param Collection $busTrips
     * @param Collection $customers
     * @param string $status
     */
    private function createSampleBookings($busTrips, $customers, int $count, $status): void
    {
        $successCount = 0;
        $errorCount = 0;
        $attemptsLeft = $count * 2; // Allow double the attempts to get the requested number

        while ($successCount < $count && $attemptsLeft > 0 && !$busTrips->isEmpty()) {
            $attemptsLeft--;

            try {
                // Select a random bus trip and customer
                $busTrip = $busTrips->random();
                $customer = $customers->random();

                // Double-check validation rules from BusTripObserver
                if ($busTrip->busDriver->travel_company_id !== $busTrip->travel_company_id) {
                    $this->command->error("Validation failed: Driver company doesn't match trip company.");
                    $errorCount++;
                    continue;
                }

                if ($busTrip->bus->travel_company_id !== $busTrip->travel_company_id) {
                    $this->command->error("Validation failed: Bus company doesn't match trip company.");
                    $errorCount++;
                    continue;
                }

                if ($busTrip->remaining_seats <= 0) {
                    $this->command->info(sprintf('Skipping trip ID %s - no seats available.', $busTrip->id));
                    // Remove this trip from the collection to avoid trying it again
                    $busTrips = $busTrips->reject(fn($trip): bool => $trip->id === $busTrip->id);
                    continue;
                }

                // Calculate a reasonable number of seats to reserve
                $seatCount = random_int(1, min(3, $busTrip->remaining_seats));

                // Create a valid seat numbers string (comma-separated)
                $totalSeats = $busTrip->number_of_seats;
                $takenBookings = BusTripBooking::where('bus_trip_id', $busTrip->id)
                    ->where('booking_status', '!=', BusTripStatusEnum::CANCELED->value)
                    ->get();

                $takenSeatNumbers = [];
                foreach ($takenBookings as $booking) {
                    $takenSeatNumbers = array_merge(
                        $takenSeatNumbers,
                        explode(',', (string) $booking->reserved_seat_numbers)
                    );
                }

                $availableSeats = array_diff(range(1, $totalSeats), $takenSeatNumbers);

                if (count($availableSeats) < $seatCount) {
                    $this->command->info(sprintf('Not enough available seats for trip ID %s.', $busTrip->id));
                    continue;
                }

                // Take the first N available seats
                $seatNumbers = array_slice($availableSeats, 0, $seatCount);
                sort($seatNumbers);
                $seatNumbersString = implode(',', $seatNumbers);

                $totalPrice = $busTrip->ticket_price * $seatCount;

                $booking = new BusTripBooking([
                    'bus_trip_id' => $busTrip->id,
                    'customer_id' => $customer->id,
                    'reserved_seat_count' => $seatCount,
                    'reserved_seat_numbers' => $seatNumbersString,
                    'total_price' => $totalPrice,
                    'booking_status' => $status,
                    'is_departure_confirmed' => $status === BusTripStatusEnum::COMPLETED->value,
                    'is_return_confirmed' => $status === BusTripStatusEnum::COMPLETED->value && $busTrip->trip_type === 'round_trip',
                ]);

                $booking->save();
                $successCount++;

                if ($status === BusTripStatusEnum::ACTIVE->value || $status === BusTripStatusEnum::COMPLETED->value) {
                    $busTrip->remaining_seats -= $seatCount;
                    $busTrip->save();

                    // If no seats remain, remove this trip from the collection
                    if ($busTrip->remaining_seats <= 0) {
                        $busTrips = $busTrips->reject(fn($trip): bool => $trip->id === $busTrip->id);
                    }
                }
            } catch (Exception $e) {
                $this->command->error("Error creating booking: " . $e->getMessage());
                $errorCount++;
                continue;
            }
        }

        $this->command->info(sprintf("Successfully created %d bookings with status '%s'. Encountered %d errors.", $successCount, $status, $errorCount));

        if ($successCount < $count) {
            $this->command->warn(sprintf("Could only create %d of %d requested bookings with status '%s'.", $successCount, $count, $status));
        }
    }
}
