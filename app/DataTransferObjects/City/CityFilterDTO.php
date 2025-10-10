<?php

declare(strict_types=1);

namespace App\DataTransferObjects\City;

/**
 * Data Transfer Object for city filtering parameters.
 */
final readonly class CityFilterDTO
{
    /**
     * @param string|null $language The language to use for city names (ar or en)
     */
    public function __construct(
        public ?string $language = null,
    ) {}

    /**
     * Convert the DTO to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'language' => $this->language,
        ];
    }

    /**
     * Create a new instance of the DTO from an array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            language: $data['language'] ?? null,
        );
    }
}
