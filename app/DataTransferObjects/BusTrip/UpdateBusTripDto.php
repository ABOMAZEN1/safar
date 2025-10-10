<?php

declare(strict_types=1);

namespace App\DataTransferObjects\BusTrip;

use Carbon\Carbon;

/**
 * Class UpdateBusTripDto.
 *
 * Data Transfer Object for updating a bus trip.
 */
final readonly class UpdateBusTripDto
{
    public function __construct(
        public ?int $fromCityId = null,
        public ?int $toCityId = null,
        public ?int $busId = null,
        public ?int $busDriverId = null,
        public ?Carbon $departureDatetime = null,
        public ?Carbon $returnDatetime = null,
        public ?string $departureTripDuration = null,
        public ?string $returnTripDuration = null,
        public ?string $tripType = null,
        public ?int $numberOfSeats = null,
        public ?string $ticketPrice = null,
    ) {}

    /**
     * Create an instance of UpdateBusTripDto from an array.
     *
     * @param array<string, mixed> $data The data to create the DTO from.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            fromCityId: isset($data['from_city_id']) ? (int) $data['from_city_id'] : null,
            toCityId: isset($data['to_city_id']) ? (int) $data['to_city_id'] : null,
            busId: isset($data['bus_id']) ? (int) $data['bus_id'] : null,
            busDriverId: isset($data['bus_driver_id']) ? (int) $data['bus_driver_id'] : null,
            departureDatetime: isset($data['departure_datetime']) ? Carbon::parse($data['departure_datetime']) : null,
            returnDatetime: isset($data['return_datetime']) ? Carbon::parse($data['return_datetime']) : null,
            departureTripDuration: isset($data['departure_trip_duration']) ? (string) $data['departure_trip_duration'] : null,
            returnTripDuration: isset($data['return_trip_duration']) ? (string) $data['return_trip_duration'] : null,
            tripType: $data['trip_type'] ?? null,
            numberOfSeats: isset($data['number_of_seats']) ? (int) $data['number_of_seats'] : null,
            ticketPrice: isset($data['ticket_price']) ? (string) $data['ticket_price'] : null,
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->fromCityId !== null) {
            $data['from_city_id'] = $this->fromCityId;
        }

        if ($this->toCityId !== null) {
            $data['to_city_id'] = $this->toCityId;
        }

        if ($this->busId !== null) {
            $data['bus_id'] = $this->busId;
        }

        if ($this->busDriverId !== null) {
            $data['bus_driver_id'] = $this->busDriverId;
        }

        if ($this->departureDatetime instanceof Carbon) {
            $data['departure_datetime'] = $this->departureDatetime->toDateTimeString();
        }

        if ($this->returnDatetime instanceof Carbon) {
            $data['return_datetime'] = $this->returnDatetime->toDateTimeString();
        }

        if ($this->departureTripDuration !== null) {
            $data['departure_trip_duration'] = $this->departureTripDuration;
        }

        if ($this->returnTripDuration !== null) {
            $data['return_trip_duration'] = $this->returnTripDuration;
        }

        if ($this->tripType !== null) {
            $data['trip_type'] = $this->tripType;
        }

        if ($this->numberOfSeats !== null) {
            $data['number_of_seats'] = $this->numberOfSeats;
        }

        if ($this->ticketPrice !== null) {
            $data['ticket_price'] = $this->ticketPrice;
        }

        return $data;
    }
}
