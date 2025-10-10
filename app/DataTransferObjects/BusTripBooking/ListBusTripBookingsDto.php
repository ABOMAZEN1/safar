<?php

declare(strict_types=1);

namespace App\DataTransferObjects\BusTripBooking;

use App\Enum\UserBookingsStatus;

/**
 * Class ListBusTripBookingsDto
 *
 * Data Transfer Object for listing bus trip bookings with filters.
 */
final readonly class ListBusTripBookingsDto
{
    /**
     * ListBusTripBookingsDto constructor.
     *
     * @param UserBookingsStatus|null $userBookingsStatus Filter bookings by status (upcoming or passed)
     */
    public function __construct(
        public ?UserBookingsStatus $userBookingsStatus = null,
    ) {}

    /**
     * Create an instance from an array.
     *
     * @param array<string, mixed> $data The request data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['status']) ? UserBookingsStatus::from($data['status']) : null,
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
            'status' => $this->userBookingsStatus?->value,
        ];
    }
}
