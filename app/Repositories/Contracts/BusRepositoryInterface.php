<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Bus;
use Illuminate\Support\Collection;

interface BusRepositoryInterface
{
    /**
     * Create a new bus.
     *
     * @param  array<string, mixed> $data The bus data
     * @return Bus                  The created bus
     */
    public function createBus(array $data): Bus;

    /**
     * Update the specified bus.
     *
     * @param  int                  $id   The bus ID
     * @param  array<string, mixed> $data The bus data
     * @return bool                 True if updated successfully
     */
    public function updateBus(int $id, array $data): bool;

    /**
     * Find bus by ID and company ID.
     *
     * @param  int      $id        The bus ID
     * @param  int      $companyId The company ID
     * @return Bus|null The bus if found, null otherwise
     */
    public function findBusByIdAndCompany(int $id, int $companyId): ?Bus;

    /**
     * Find bus by ID.
     *
     * @param  int      $id The bus ID
     * @return Bus|null The bus if found, null otherwise
     */
    public function findBusById(int $id): ?Bus;

    /**
     * Get buses by company ID.
     *
     * @param  int                  $companyId The company ID
     * @return Collection<int, Bus> Collection of buses
     */
    public function getBusesByCompanyId(int $companyId): Collection;

    /**
     * Get the number of buses for a company.
     *
     * @param  int $companyId The company ID
     * @return int The number of buses
     */
    public function getNumberOfBuses(int $companyId): int;
}
