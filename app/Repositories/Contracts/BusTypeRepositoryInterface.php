<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\BusType;
use Illuminate\Support\Collection;

/**
 * Interface BusTypeRepositoryInterface.
 */
interface BusTypeRepositoryInterface
{
    /**
     * Retrieve all bus types.
     *
     * @return Collection<int, BusType>
     */
    public function getAllBusTypes(): Collection;
}
