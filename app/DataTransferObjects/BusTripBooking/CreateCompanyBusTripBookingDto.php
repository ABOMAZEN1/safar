<?php

declare(strict_types=1);

namespace App\DataTransferObjects\BusTripBooking;

/**
 * Data Transfer Object for creating a bus trip booking by company.
 */
final class CreateCompanyBusTripBookingDto
{
    /**
     * Constructor.
     */
    public function __construct(
        public int $busTripId,
        public string $customerName,
        public string $phoneNumber,
        public string $nationalId,
        public string $gender,
        public string $birthDate,
        public string $address,
        public string $motherName,
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
            customerName: (string) $data['customer_name'],
            phoneNumber: (string) $data['phone_number'],
            nationalId: (string) $data['national_id'],
            gender: (string) $data['gender'],
            birthDate: (string) $data['birth_date'],
            address: (string) $data['address'],
            motherName: (string) $data['mother_name'],
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
            'customer_name' => $this->customerName,
            'phone_number' => $this->phoneNumber,
            'national_id' => $this->nationalId,
            'gender' => $this->gender,
            'birth_date' => $this->birthDate,
            'address' => $this->address,
            'mother_name' => $this->motherName,
            'reserved_seat_count' => $this->reservedSeatCount,
            'companions' => $this->companions,
        ];
    }
}
