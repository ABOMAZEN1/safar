<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DataTransferObjects\BusTrip\TripFilterDTO;
use App\Models\BusTrip;
use Illuminate\Support\Collection;
use App\DataTransferObjects\BusTrip\CreateBusTripDto;

interface BusTripRepositoryInterface
{
    /**
     * Find a bus trip by its ID.
     */
    public function findTripById(int $busTripId): ?BusTrip;

    /**
     * Update the remaining seats for a bus trip.
     */
    public function updateRemainingSeats(BusTrip $busTrip, int $remainingSeats): void;

    /**
     * Get a collection of bus trips based on filter criteria.
     */
    public function getTrips(TripFilterDTO $tripFilterDTO): Collection;

    /**
     * Create a new bus trip.
     */
    public function createBusTrip(CreateBusTripDto $createBusTripDto): BusTrip;

    /**
     * Get a collection of trips for a specific company.
     */
    public function getCompanyTrips(int $companyId): Collection;

    /**
     * Update an existing bus trip with new data.
     */
    public function updateBusTrip(BusTrip $busTrip, array $updateBusTripData): void;

    /**
     * Get a collection of trips for a specific driver.
     */
    public function getDriverTrips(int $driverId): Collection;

    /**
     * Find a bus trip with its details by ID.
     */
    public function findTripWithDetails(int $tripId): ?BusTrip;

    /**
     * Get the number of completed trips for a specific company.
     */
    public function getNumberOfCompletedTrips(int $companyId): int;

    /**
     * Find a bus trip by its ID and lock it for update.
     */
    public function findTripByIdAndLockForUpdate(int $tripId): ?BusTrip;
}
