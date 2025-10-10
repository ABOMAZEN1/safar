<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\BusType;
use Illuminate\Support\Collection;

/**
 * Class BusTypeRepository.
 *
 * Handles the retrieval and management of bus types.
 *
 */
final readonly class BusTypeRepository
{
    public function __construct(
        private BusType $busType,
    ) {}

    /**
     * Get all bus types.
     *
     * @return Collection<int, BusType> Collection of bus types
     */
    public function getAllBusTypes(): Collection
    {
        return $this->busType
            ->get();
    }
}
