<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\BusDriver;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class BusDriverRepository.
 *
 * Handles the creation and retrieval of bus drivers.
 */
final readonly class BusDriverRepository
{
    public function __construct(
        private BusDriver $busDriver,
    ) {}

    /**
     * Create a new bus driver.
     *
     * @param  array<string, mixed> $data The bus driver data
     * @return BusDriver            The created bus driver
     */
    public function createBusDriver(array $data): BusDriver
    {
        return $this->busDriver->create($data);
    }

    /**
     * Get all drivers for a specific company.
     *
     * @param  int                        $companyId The ID of the company
     * @return Collection<int, BusDriver> Collection of bus drivers with their associated users
     */
    public function getCompanyDrivers(int $companyId): Collection
    {
        return $this->busDriver->with('user')
            ->where('travel_company_id', $companyId)
            ->get();
    }

    /**
     * Get a specific bus driver by ID.
     *
     * @param  int            $id The ID of the bus driver
     * @return BusDriver|null The bus driver if found, null otherwise
     */
    public function getBusDriver(int $id): ?BusDriver
    {
        return $this->busDriver->findOrFail($id);
    }

    /**
     * Get the total number of bus drivers for a company.
     *
     * @param  int $companyId The ID of the company
     * @return int The number of bus drivers
     */
    public function getNumberOfBusDrivers(int $companyId): int
    {
        return $this->busDriver->where('travel_company_id', $companyId)->count();
    }

    /**
     * Find a bus driver by phone number.
     *
     * @param string $phoneNumber The phone number to search for
     * @return BusDriver The bus driver if found
     * @throws ModelNotFoundException If bus driver not found
     */
    public function findBusDriverByPhoneNumber(string $phoneNumber): BusDriver
    {
        return $this->busDriver->whereHas('user', function (Builder $builder) use ($phoneNumber): void {
            $builder->where('phone_number', $phoneNumber);
        })->with('user')->firstOrFail();
    }

    /**
     * Delete a bus driver by ID.
     *
     * @param  int $id The ID of the bus driver to delete
     * @return bool Whether the deletion was successful
     */
    public function deleteBusDriver(int $id): bool
    {
        return $this->busDriver->findOrFail($id)->delete();
    }

    /**
     * Get all bus drivers for a specific travel company.
     *
     * @param int $travelCompanyId The ID of the travel company
     * @return \Illuminate\Database\Eloquent\Collection The collection of bus drivers
     */
    public function getCompanyBusDrivers(int $travelCompanyId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->busDriver
            ->where('travel_company_id', $travelCompanyId)
            ->with('user')
            ->get();
    }
}
