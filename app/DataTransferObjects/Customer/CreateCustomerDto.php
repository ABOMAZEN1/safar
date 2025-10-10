<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Customer;

use App\Enum\CustomerGenderEnum;

/**
 * Class CreateCustomerDto.
 *
 * Data Transfer Object for creating a customer.
 */
final readonly class CreateCustomerDto
{
    /**
     * CreateCustomerDto constructor.
     *
     * @param string             $nationalId             The national ID of the customer.
     * @param CustomerGenderEnum $customerGenderEnum     The gender of the customer.
     * @param string             $address                The address of the customer.
     * @param string             $motherName             The mother's name of the customer.
     * @param string|null        $preferredContactMethod The preferred contact method of the customer.
     */
    public function __construct(
        public string $birthDate,
        public string $nationalId,
        public CustomerGenderEnum $customerGenderEnum,
        public string $address,
        public string $motherName,
    ) {}

    /**
     * Create an instance of CreateCustomerDto from an array.
     *
     * @param array<string, string|CustomerGenderEnum|null> $data The data to create the DTO from.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            birthDate: $data['birth_date'],
            nationalId: $data['national_id'],
            customerGenderEnum: CustomerGenderEnum::from($data['gender']),
            address: $data['address'],
            motherName: $data['mother_name'],
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'birth_date' => $this->birthDate,
            'national_id' => $this->nationalId,
            'gender' => $this->customerGenderEnum->value,
            'address' => $this->address,
            'mother_name' => $this->motherName,
        ];
    }
}
