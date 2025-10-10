<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\TravelCompanion;
use Illuminate\Support\Collection;

interface TravelCompanionRepositoryInterface
{
    /**
     * Create a new companion.
     *
     * @param array<string, mixed> $data
     */
    public function createTravelCompanion(array $data): TravelCompanion;

    /**
     * Insert a new companion.
     *
     * @param array<string, mixed> $data
     */
    public function insertTravelCompanion(array $data): void;

    /**
     * Get all travel companions.
     *
     * @return Collection<int, TravelCompanion>
     */
    public function getAllTravelCompanions(): Collection;
}
