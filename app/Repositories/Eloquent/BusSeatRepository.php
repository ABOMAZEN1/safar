<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\BusSeat;
use App\Models\BusTripBooking;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final readonly class BusSeatRepository
{
    public function __construct(
        private BusSeat $busSeat,
    ) {}

    public function insertBusSeats(array $seats): void
    {
        DB::transaction(function () use ($seats): void {
            $this->busSeat->insert($seats);
        });
    }

    public function getAvailableSeats(int $busTripId): Collection
    {
        return DB::transaction(function () use ($busTripId): Collection {
            $availableSeats = $this->busSeat
                ->forTrip($busTripId)
                ->available()
                ->get();

            $takenSeatNumbers = $this->getAlreadyBookedSeatNumbers($busTripId);

            if ($takenSeatNumbers !== []) {
                return $availableSeats->filter(fn($seat): bool => !in_array($seat->seat_number, $takenSeatNumbers));
            }

            return $availableSeats;
        });
    }

    /**
     * Update a bus seat with provided data.
     *
     * @param BusSeat              $busSeat The bus seat to update
     * @param array<string, mixed> $data    The update data
     */
    public function updateBusSeat(BusSeat $busSeat, array $data): void
    {
        DB::transaction(function () use ($busSeat, $data): void {
            $busSeat->update($data);
        });
    }

    /**
     * Reserve first available seats and lock them.
     *
     * @param  int                      $busTripId The bus trip ID
     * @param  int                      $numSeats  Number of seats to reserve
     * @return Collection<int, BusSeat> Collection of reserved seats
     */
    public function reserveFirstAvailableSeatsAndLock(int $busTripId, int $numSeats): Collection
    {
        $bookedSeatNumbers = $this->getAlreadyBookedSeatNumbers($busTripId);

        return DB::transaction(function () use ($busTripId, $numSeats, $bookedSeatNumbers): Collection {
            // Get available seats that are not in active bookings using scopes
            $availableSeats = $this->busSeat
                ->forTrip($busTripId)
                ->available()
                ->when($bookedSeatNumbers !== [], fn($query) => $query->notInSeatNumbers($bookedSeatNumbers))
                ->orderBy('seat_number')
                ->take($numSeats)
                ->get();

            if ($availableSeats->count() < $numSeats) {
                return collect([]);
            }

            $busSeatIds = $availableSeats->pluck('id')->toArray();

            $this->busSeat
                ->whereIn('id', $busSeatIds)
                ->update([
                    'is_reserved' => true,
                    'updated_at' => now(),
                ]);

            return $availableSeats;
        });
    }

    /**
     * Make specific seats available for a trip.
     *
     * @param array<int, int> $seatNumbers Array of seat numbers
     * @param int             $busTripId   The bus trip ID
     */
    public function makeSeatsAvailable(array $seatNumbers, int $busTripId): void
    {
        DB::transaction(function () use ($seatNumbers, $busTripId): void {
            $this->busSeat
                ->forTrip($busTripId)
                ->whereIn('seat_number', $seatNumbers)
                ->update(['is_reserved' => false]);
        });
    }

    /**
     * Get all seat numbers that are already booked in active bookings
     * 
     * @param int $busTripId The bus trip ID
     * @return array<int, int> Array of booked seat numbers
     */
    private function getAlreadyBookedSeatNumbers(int $busTripId): array
    {
        $bookings = BusTripBooking::forTrip($busTripId)
            ->notCanceled()
            ->withReservedSeats()
            ->get();

        $bookedSeatNumbers = [];
        foreach ($bookings as $booking) {
            $seatNumbers = explode(',', (string) $booking->reserved_seat_numbers);
            $bookedSeatNumbers = array_merge($bookedSeatNumbers, $seatNumbers);
        }

        return array_map('intval', array_unique($bookedSeatNumbers));
    }
}
