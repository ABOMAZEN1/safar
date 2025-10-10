<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\DataTransferObjects\BusTrip\TripFilterDTO;
use App\DataTransferObjects\BusTrip\CreateBusTripDto;
use App\Enum\BookingStatusEnum;
use App\Models\BusTrip;
use Illuminate\Support\Collection;
use App\Enum\TimeCategoryEnum;
use App\Enum\OrderByEnum;

/**
 * Class BusTripRepository.
 *
 * Handles the creation, retrieval, and management of bus trips.
 *
 */
final readonly class BusTripRepository
{
    public function __construct(
        private BusTrip $busTrip,
    ) {}

    /**
     * Find a bus trip by its ID.
     *
     * @param  int          $tripId The trip ID
     * @return BusTrip|null The bus trip if found, null otherwise
     */
    public function findTripById(int $tripId): ?BusTrip
    {
        return $this->busTrip->findOrFail($tripId);
    }

    /**
     * Update remaining seats for a bus trip.
     *
     * @param BusTrip $busTrip        The bus trip to update
     * @param int     $remainingSeats The new number of remaining seats
     */
    public function updateRemainingSeats(BusTrip $busTrip, int $remainingSeats): void
    {
        $busTrip->update(['remaining_seats' => $remainingSeats]);
    }

    /**
     * Get filtered trips based on search criteria.
     *
     * @param  TripFilterDTO            $tripFilterDTO The filter criteria
     * @return Collection<int, BusTrip> Collection of filtered bus trips
     */
    public function getTrips(TripFilterDTO $tripFilterDTO): Collection
    {
        $builder = $this->busTrip->with([
            'travelCompany',
            'bus',
            'bus.busType',
            'fromCity',
            'toCity',
            'busDriver',
            'busDriver.user',
        ]);

        if ($tripFilterDTO->fromCityId !== null) {
            $builder->where('from_city_id', $tripFilterDTO->fromCityId);
        }

        if ($tripFilterDTO->toCityId !== null) {
            $builder->where('to_city_id', $tripFilterDTO->toCityId);
        }

        if ($tripFilterDTO->tripType !== null) {
            $builder->where('trip_type', $tripFilterDTO->tripType);
        }

        if ($tripFilterDTO->departureDatetime !== null) {
            $builder->whereDate('departure_datetime', $tripFilterDTO->departureDatetime);
        }

        if ($tripFilterDTO->returnDatetime !== null) {
            $builder->whereDate('return_datetime', $tripFilterDTO->returnDatetime);
        }

        if ($tripFilterDTO->requiredSeats !== null) {
            $builder->where('remaining_seats', '>=', $tripFilterDTO->requiredSeats);
        }

        if ($tripFilterDTO->minPrice !== null) {
            $builder->where('ticket_price', '>=', $tripFilterDTO->minPrice);
        }

        if ($tripFilterDTO->maxPrice !== null) {
            $builder->where('ticket_price', '<=', $tripFilterDTO->maxPrice);
        }

        if ($tripFilterDTO->timeCategory !== null) {
            $this->applyTimeCategory($builder, $tripFilterDTO->timeCategory);
        }

        if ($tripFilterDTO->busTypeId !== null) {
            $builder->whereHas('bus', function ($q) use ($tripFilterDTO): void {
                $q->where('bus_type_id', $tripFilterDTO->busTypeId);
            });
        }

        if ($tripFilterDTO->travelCompanyId !== null) {
            $builder->where('travel_company_id', $tripFilterDTO->travelCompanyId);
        }

        if ($tripFilterDTO->orderBy !== null) {
            $this->applyOrdering($builder, $tripFilterDTO->orderBy);
        }

        return $builder->get();
    }

    /**
     * Create a new bus trip.
     *
     * @param  CreateBusTripDto $createBusTripDto The bus trip data
     * @return BusTrip          The created bus trip
     */
    public function createBusTrip(CreateBusTripDto $createBusTripDto): BusTrip
    {
        return $this->busTrip->create($createBusTripDto->toArray());
    }

    /**
     * Get all trips for a specific company.
     *
     * @param  int                      $companyId The company ID
     * @return Collection<int, BusTrip> Collection of company trips
     */
    public function getCompanyTrips(int $companyId): Collection
    {
        return $this->busTrip
            ->with([
                'travelCompany',
                'bus',
                'bus.busType',
                'fromCity',
                'toCity',
                'busDriver',
            ])
            ->where('travel_company_id', $companyId)
            ->get();
    }

    /**
     * Get all trips for a specific driver.
     *
     * @param  int                      $driverId The driver ID
     * @return Collection<int, BusTrip> Collection of driver trips
     */
    public function getDriverTrips(int $driverId): Collection
    {
        return $this->busTrip
            ->with([
                'toCity',
                'fromCity',
            ])
            ->withCount([
                'bookings as active_bookings' => static function ($query): void {
                    $query->where('booking_status', '!=', BookingStatusEnum::CANCELED->value);
                },
                'bookings as departure_confirmed_bookings' => static function ($query): void {
                    $query->where('is_departure_confirmed', true)
                        ->where('booking_status', '!=', BookingStatusEnum::CANCELED->value);
                },
                'bookings as return_confirmed_bookings' => static function ($query): void {
                    $query->where('is_return_confirmed', true)
                        ->where('booking_status', '!=', BookingStatusEnum::CANCELED->value);
                },
            ])
            ->where('bus_driver_id', $driverId)
            ->orderBy('departure_datetime', 'desc')
            ->get();
    }

    /**
     * Find a trip with all its details.
     *
     * @param  int          $tripId The trip ID
     * @return BusTrip|null The bus trip with details if found, null otherwise
     */
    public function findTripWithDetails(int $tripId): ?BusTrip
    {
        return $this->busTrip
            ->with([
                'travelCompany',
                'fromCity',
                'toCity',
                'bus.busType',
                'busDriver.user',
            ])
            ->where('id', $tripId)
            ->firstOrFail();
    }

    /**
     * Get the number of completed trips for a company.
     *
     * @param  int $companyId The company ID
     * @return int The number of completed trips
     */
    public function getNumberOfCompletedTrips(int $companyId): int
    {
        return $this->busTrip
            ->where('travel_company_id', $companyId)
            ->where('departure_datetime', '<', now()->format('Y-m-d H:i:s'))
            ->orWhere(static function ($query): void {
                $query->where('departure_datetime', '=', now()->format('Y-m-d H:i:s'));
            })
            ->count();
    }

    /**
     * Find a trip by ID and lock it for update.
     *
     * @param  int          $tripId The trip ID
     * @return BusTrip|null The bus trip if found, null otherwise
     */
    public function findTripByIdAndLockForUpdate(int $tripId): ?BusTrip
    {
        return $this->busTrip
            ->where('id', $tripId)
            ->lockForUpdate()
            ->firstOrFail();
    }

    /**
     * Update an existing bus trip with new data.
     *
     * @param BusTrip              $busTrip           The bus trip to update
     * @param array<string, mixed> $updateBusTripData The new data for the bus trip
     * @return BusTrip The updated bus trip
     */
    public function updateBusTrip(BusTrip $busTrip, array $updateBusTripData): BusTrip
    {
        $busTrip->update($updateBusTripData);

        $busTrip->refresh();

        return $busTrip;
    }

    /**
     * Apply time category filter to the query.
     */
    private function applyTimeCategory($query, string $timeCategory): void
    {
        $timeRanges = [
            TimeCategoryEnum::MORNING->value => ['06:00:00', '11:59:59'],
            TimeCategoryEnum::AFTERNOON->value => ['12:00:00', '17:59:59'],
            TimeCategoryEnum::EVENING->value => ['18:00:00', '23:59:59'],
            TimeCategoryEnum::NIGHT->value => ['00:00:00', '05:59:59'],
        ];

        if (isset($timeRanges[$timeCategory])) {
            [$start, $end] = $timeRanges[$timeCategory];
            $query->whereTime('departure_datetime', '>=', $start)
                ->whereTime('departure_datetime', '<=', $end);
        }
    }

    /**
     * Apply ordering to the query.
     */
    private function applyOrdering($query, string $orderBy): void
    {
        match ($orderBy) {
            OrderByEnum::PRICE_ASC->value => $query->orderBy('ticket_price', 'asc'),
            OrderByEnum::PRICE_DESC->value => $query->orderBy('ticket_price', 'desc'),
            OrderByEnum::DEPARTURE_TIME_ASC->value => $query->orderBy('departure_datetime', 'asc'),
            OrderByEnum::DEPARTURE_TIME_DESC->value => $query->orderBy('departure_datetime', 'desc'),
            OrderByEnum::AVAILABLE_SEATS_DESC->value => $query->orderBy('remaining_seats', 'desc'),
            default => $query->orderBy('departure_datetime', 'asc'),
        };
    }
}
