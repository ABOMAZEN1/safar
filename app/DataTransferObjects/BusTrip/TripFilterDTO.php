<?php

declare(strict_types=1);

namespace App\DataTransferObjects\BusTrip;

final readonly class TripFilterDTO
{
    public function __construct(
        public ?int $fromCityId,
        public ?int $toCityId,
        public ?string $tripType,
        public ?string $departureDatetime,
        public ?string $returnDatetime,
        public ?int $requiredSeats,
        public ?float $minPrice,
        public ?float $maxPrice,
        public ?string $timeCategory,
        public ?int $busTypeId,
        public ?string $orderBy,
        public ?int $travelCompanyId,
    ) {}

    /**
     * Create an instance of TripFilterDTO from an array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            fromCityId: isset($data['from_city']) ? (int) $data['from_city'] : null,
            toCityId: isset($data['to_city']) ? (int) $data['to_city'] : null,
            tripType: $data['type'] ?? null,
            departureDatetime: $data['departure_datetime'] ?? null,
            returnDatetime: $data['return_datetime'] ?? null,
            requiredSeats: isset($data['required_seats']) ? (int) $data['required_seats'] : null,
            minPrice: isset($data['min_price']) ? (float) $data['min_price'] : null,
            maxPrice: isset($data['max_price']) ? (float) $data['max_price'] : null,
            timeCategory: $data['time_category'] ?? null,
            busTypeId: isset($data['bus_type_id']) ? (int) $data['bus_type_id'] : null,
            orderBy: $data['order_by'] ?? null,
            travelCompanyId: isset($data['travel_company_id']) ? (int) $data['travel_company_id'] : null,
        );
    }

    public static function validateData(array $data): bool
    {
        return isset($data['from_city_id'], $data['to_city_id'], $data['departure_datetime']);
    }

    public function toArray(): array
    {
        return [
            'from_city_id' => $this->fromCityId,
            'to_city_id' => $this->toCityId,
            'trip_type' => $this->tripType,
            'departure_datetime' => $this->departureDatetime,
            'return_datetime' => $this->returnDatetime,
            'required_seats' => $this->requiredSeats,
            'min_price' => $this->minPrice,
            'max_price' => $this->maxPrice,
            'time_category' => $this->timeCategory,
            'bus_type_id' => $this->busTypeId,
            'order_by' => $this->orderBy,
            'travel_company_id' => $this->travelCompanyId,
        ];
    }
}
