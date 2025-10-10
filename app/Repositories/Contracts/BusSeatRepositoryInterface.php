<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\BusSeat;
use Illuminate\Support\Collection;

interface BusSeatRepositoryInterface
{
    /**
     * Insert multiple bus seats.
     */
    public function insertBusSeats(array $seats): void;

    /**
     * Get available seats for a specific trip.
     */
    public function getAvailableSeats(int $tripId): Collection;

    /**
     * Update a specific bus seat.
     */
    public function updateBusSeat(BusSeat $busSeat, array $data): void;

    /**
     * Reserve and lock the first available seats for a specific trip.
     */
    public function reserveFirstAvailableSeatsAndLock(int $tripId, int $numSeats): Collection;

    /**
     * Make specific seats available for a specific trip.
     */
    public function makeSeatsAvailable(array $seatsNumbers, int $tripId): void;
}
