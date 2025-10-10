<?php

declare(strict_types=1);

namespace App\DataTransferObjects\TravelCompany;

final readonly class CompanyFilterDTO
{
    public function __construct(
        public ?string $name,
        public ?string $address,
        public ?string $orderBy,
        public ?bool $hasBuses,
        public ?bool $hasActiveTrips,
    ) {}

    /**
     * Create an instance of CompanyFilterDTO from an array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            address: $data['address'] ?? null,
            orderBy: $data['order_by'] ?? null,
            hasBuses: isset($data['has_buses']) ? (bool) $data['has_buses'] : null,
            hasActiveTrips: isset($data['has_active_trips']) ? (bool) $data['has_active_trips'] : null,
        );
    }

    /**
     * Convert DTO to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'order_by' => $this->orderBy,
            'has_buses' => $this->hasBuses,
            'has_active_trips' => $this->hasActiveTrips,
        ];
    }
}
