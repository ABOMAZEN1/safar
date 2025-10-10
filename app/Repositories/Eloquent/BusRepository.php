<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Bus;
use Illuminate\Support\Collection;

/**
 * Class BusRepository.
 *
 * Handles the creation, retrieval, and management of buses.
 *
 */
final readonly class BusRepository
{
    public function __construct(
        private Bus $bus,
    ) {}

    /**
     * Create a new bus.
     *
     * @param  array<string, mixed> $data The bus data
     * @return Bus                  The created bus
     */
    public function createBus(array $data): Bus
    {
        return $this->bus->create($data);
    }

    /**
     * Update the specified bus.
     *
     * @param  int                  $id   The bus ID
     * @param  array<string, mixed> $data The bus data
     * @return bool                 True if updated successfully
     */
    public function updateBus(int $id, array $data): bool
    {
        return (bool) $this->bus
            ->where('id', $id)
            ->update($data);
    }

    /**
     * Find bus by ID and company ID.
     *
     * @param  int      $id        The bus ID
     * @param  int      $companyId The company ID
     * @return Bus|null The bus if found, null otherwise
     */
    public function findBusByIdAndCompany(int $id, int $companyId): ?Bus
    {
        return $this->bus
            ->where('id', $id)
            ->where('travel_company_id', $companyId)
            ->firstOrFail();
    }

    /**
     * Find bus by ID.
     *
     * @param  int      $id The bus ID
     * @return Bus|null The bus if found, null otherwise
     */
    public function findBusById(int $id): ?Bus
    {
        return $this->bus->findOrFail($id);
    }

    /**
     * Get buses by company ID.
     *
     * @param  int                  $companyId The company ID
     * @return Collection<int, Bus> Collection of buses with their bus types
     */
    public function getBusesByCompanyId(int $companyId): Collection
    {
        return $this->bus
            ->where('travel_company_id', $companyId)
            ->with(['busType'])
            ->get();
    }

    /**
     * Get the number of buses for a company.
     *
     * @param  int $companyId The company ID
     * @return int The number of buses
     */
    public function getNumberOfBuses(int $companyId): int
    {
        return $this->bus
            ->where('travel_company_id', $companyId)
            ->count();
    }

    /**
     * Delete the specified bus.
     *
     * @param int $id The ID of the bus to delete
     * @return bool Whether the deletion was successful
     */
    public function deleteBus(int $id): bool
    {
        return (bool) $this->bus->findOrFail($id)->delete();
    }
}
