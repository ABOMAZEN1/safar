<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\TravelCompanion;
use Illuminate\Support\Collection;

/**
 * Class TravelCompanionRepository.
 *
 * Handles the creation, retrieval, and management of travel companions.
 *
 */
final readonly class TravelCompanionRepository
{
    public function __construct(
        private TravelCompanion $travelCompanion,
    ) {}

    /**
     * Create a new companion.
     *
     * @param  array<string, string|int|float|bool|null> $data The companion data
     * @return TravelCompanion                           The created travel companion
     */
    public function createTravelCompanion(array $data): TravelCompanion
    {
        return $this->travelCompanion->create($data);
    }

    /**
     * Insert a new companion.
     *
     * @param array<string, string|int|float|bool|null> $data The companion data
     */
    public function insertTravelCompanion(array $data): void
    {
        $this->travelCompanion->insert($data);
    }

    /**
     * Get all travel companions.
     *
     * @return Collection<int, TravelCompanion> Collection of travel companions
     */
    public function getAllTravelCompanions(): Collection
    {
        return $this->travelCompanion
            ->get();
    }
}
