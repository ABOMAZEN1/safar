<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\BusTrip;
use Exception;
use Illuminate\Support\Facades\Log;

final class BusTripObserver
{
    public function creating(BusTrip $busTrip): void
    {
        $this->validateSeatCapacity($busTrip);

        $busTrip->remaining_seats = $busTrip->number_of_seats;
    }

    public function saving(BusTrip $busTrip): void
    {
        if ($busTrip->isDirty('bus_id') && $busTrip->bus !== null) {
            $this->validateBusBelongsToCompany($busTrip);
        }

        if ($busTrip->isDirty('bus_driver_id') && $busTrip->busDriver !== null) {
            $this->validateDriverBelongsToCompany($busTrip);
        }
    }

    public function updating(BusTrip $busTrip): void
    {
        $this->validateSeatCapacity($busTrip);
    }

    /**
     * Validate that the number of seats does not exceed the bus capacity.
     *
     * @param BusTrip $busTrip The bus trip to validate
     * @throws Exception If the number of seats exceeds the bus capacity
     */
    private function validateSeatCapacity(BusTrip $busTrip): void
    {
        if ($busTrip->number_of_seats > $busTrip->bus->capacity) {
            $errorMessage = __('messages.errors.bus_trip.insufficient_seats');

            Log::error('BusTrip seat capacity validation failed', [
                'trip_id' => $busTrip->id ?? 'new_trip',
                'bus_id' => $busTrip->bus_id,
                'requested_seats' => $busTrip->number_of_seats,
                'bus_capacity' => $busTrip->bus->capacity,
                'error' => $errorMessage,
                'resolution' => 'Reduce the number of seats to be less than or equal to the bus capacity, or assign a bus with larger capacity.'
            ]);

            throw new Exception($errorMessage);
        }
    }

    /**
     * Validate that the bus belongs to the same company as the trip.
     *
     * @param BusTrip $busTrip The bus trip to validate
     * @throws Exception If the bus does not belong to the trip company
     */
    private function validateBusBelongsToCompany(BusTrip $busTrip): void
    {
        if ($busTrip->bus->travel_company_id !== $busTrip->travel_company_id) {
            $errorMessage = __('messages.errors.bus_trip.access_denied');

            Log::error('BusTrip company ownership validation failed', [
                'trip_id' => $busTrip->id ?? 'new_trip',
                'bus_id' => $busTrip->bus_id,
                'bus_company_id' => $busTrip->bus->travel_company_id,
                'trip_company_id' => $busTrip->travel_company_id,
                'error' => $errorMessage,
                'resolution' => "Either change the bus to one owned by the trip's company, or change the trip's company to match the bus's company."
            ]);

            throw new Exception($errorMessage);
        }
    }

    /**
     * Validate that the bus driver belongs to the same company as the trip.
     *
     * @param BusTrip $busTrip The bus trip to validate
     * @throws Exception If the driver does not belong to the trip company
     */
    private function validateDriverBelongsToCompany(BusTrip $busTrip): void
    {
        if ($busTrip->busDriver && $busTrip->busDriver->travel_company_id !== $busTrip->travel_company_id) {
            $errorMessage = __('messages.errors.bus_trip.access_denied');

            Log::error('BusDriver company association validation failed', [
                'trip_id' => $busTrip->id ?? 'new_trip',
                'driver_id' => $busTrip->bus_driver_id,
                'driver_name' => $busTrip->busDriver->user->name ?? 'Unknown',
                'driver_company_id' => $busTrip->busDriver->travel_company_id,
                'trip_company_id' => $busTrip->travel_company_id,
                'error' => $errorMessage,
                'resolution' => "Either assign a driver from the same company as the trip, or update the trip's company to match the driver's company."
            ]);

            throw new Exception($errorMessage);
        }
    }
}
