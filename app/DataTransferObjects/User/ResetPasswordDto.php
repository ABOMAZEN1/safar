<?php

declare(strict_types=1);

namespace App\DataTransferObjects\User;

final readonly class ResetPasswordDto
{
    public function __construct(
        public string $phone_number,
        public string $current_password,
        public string $new_password,
    ) {}

    /**
     * Create a DTO from an array of data.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            phone_number: $data['phone_number'],
            current_password: $data['current_password'],
            new_password: $data['new_password'],
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
            'phone_number' => $this->phone_number,
            'current_password' => $this->current_password,
            'new_password' => $this->new_password,
        ];
    }
}
