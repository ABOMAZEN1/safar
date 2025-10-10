<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Customer;

use App\Enum\CustomerGenderEnum;

final readonly class UpdateCustomerInformationDto
{
    public function __construct(
        public ?string $name = null,
        public ?string $birth_date = null,
        public ?string $national_id = null,
        public ?string $gender = null,
        public ?string $address = null,
        public ?string $mother_name = null,
    ) {}

    /**
     * Create a DTO from an array of data.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            birth_date: $data['birth_date'] ?? null,
            national_id: $data['national_id'] ?? null,
            gender: $data['gender'] ?? null,
            address: $data['address'] ?? null,
            mother_name: $data['mother_name'] ?? null,
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'birth_date' => $this->birth_date,
            'national_id' => $this->national_id,
            'gender' => $this->gender,
            'address' => $this->address,
            'mother_name' => $this->mother_name,
        ], fn($value): bool => $value !== null);
    }
}
