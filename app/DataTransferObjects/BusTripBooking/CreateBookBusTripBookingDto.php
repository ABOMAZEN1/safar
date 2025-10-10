<?php

declare(strict_types=1);

namespace App\DataTransferObjects\BusTripBooking;

/**
 * Data Transfer Object for creating a bus trip booking from a customer.
 */
final class CreateBookBusTripBookingDto
{
    /**
     * Constructor.
     */
    public function __construct(
        public int $busTripId,
        public int $reservedSeatCount,
        public array $companions
    ) {}

    /**
     * Create a DTO from an array of data.
     * 
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            busTripId: (int) $data['bus_trip_id'],
            reservedSeatCount: (int) $data['reserved_seat_count'],
            companions: $data['companions'] ?? []
        );
    }

    /**
     * Convert the DTO to an array.
     * 
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'bus_trip_id' => $this->busTripId,
            'reserved_seat_count' => $this->reservedSeatCount,
            'companions' => $this->companions,
        ];
    }
}
