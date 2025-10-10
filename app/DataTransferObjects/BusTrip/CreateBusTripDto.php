<?php

declare(strict_types=1);

namespace App\DataTransferObjects\BusTrip;

use Carbon\Carbon;

/**
 * Class CreateBusTripDto.
 *
 * Data Transfer Object for creating a bus trip.
 */
final class CreateBusTripDto
{
    private ?int $travelCompanyId = null;

    public function __construct(
        public int $fromCityId,
        public int $toCityId,
        public int $busId,
        public int $busDriverId,
        public Carbon $departureDatetime,
        public ?Carbon $returnDatetime,
        public string $durationOfDepartureTrip,
        public ?string $durationOfReturnTrip,
        public string $tripType,
        public int $numberOfSeats,
        public string $ticketPrice,
    ) {}

    /**
     * Create an instance of CreateBusTripDto from an array.
     *
     * @param array<string, mixed> $data The data to create the DTO from.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            fromCityId: (int) $data['from_city_id'],
            toCityId: (int) $data['to_city_id'],
            busId: (int) $data['bus_id'],
            busDriverId: (int) $data['bus_driver_id'],
            departureDatetime: Carbon::parse($data['departure_datetime']),
            returnDatetime: isset($data['return_datetime']) ? Carbon::parse($data['return_datetime']) : null,
            durationOfDepartureTrip: (string) $data['duration_of_departure_trip'],
            durationOfReturnTrip: isset($data['duration_of_return_trip']) ? (string) $data['duration_of_return_trip'] : null,
            tripType: $data['trip_type'],
            numberOfSeats: (int) $data['number_of_seats'],
            ticketPrice: (string) $data['ticket_price'],
        );
    }

    public function setTravelCompanyId(int $travelCompanyId): self
    {
        $this->travelCompanyId = $travelCompanyId;

        return $this;
    }

    public function getTravelCompanyId(): ?int
    {
        return $this->travelCompanyId;
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'from_city_id' => $this->fromCityId,
            'to_city_id' => $this->toCityId,
            'bus_id' => $this->busId,
            'bus_driver_id' => $this->busDriverId,
            'travel_company_id' => $this->travelCompanyId,
            'departure_datetime' => $this->departureDatetime->toDateTimeString(),
            'return_datetime' => $this->returnDatetime?->toDateTimeString(),
            'duration_of_departure_trip' => $this->durationOfDepartureTrip,
            'duration_of_return_trip' => $this->durationOfReturnTrip,
            'trip_type' => $this->tripType,
            'number_of_seats' => $this->numberOfSeats,
            'ticket_price' => $this->ticketPrice,
        ];
    }
}
