<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\BusDriver;
use Illuminate\Support\Collection;

interface BusDriverRepositoryInterface
{
    /**
     * Create a new bus driver.
     *
     * @param  array<string, mixed> $data The data of the bus driver
     * @return BusDriver            The created bus driver
     */
    public function createBusDriver(array $data): BusDriver;

    /**
     * Get all drivers of a company.
     *
     * @param  int                        $companyId The ID of the company
     * @return Collection<int, BusDriver> The collection of bus drivers
     */
    public function getCompanyDrivers(int $companyId): Collection;

    /**
     * Edit a bus driver.
     *
     * @param  int       $id   The ID of the bus driver
     * @param  array     $data The new data of the bus driver
     * @return BusDriver The updated bus driver
     */
    public function editBusDriver(int $id, array $data): BusDriver;

    /**
     * Get a bus driver by ID.
     *
     * @param  int            $id The ID of the bus driver
     * @return BusDriver|null The bus driver if found, null otherwise
     */
    public function getBusDriver(int $id): ?BusDriver;

    /**
     * Get the number of bus drivers in a company.
     *
     * @param  int $companyId The ID of the company
     * @return int The number of bus drivers
     */
    public function getNumberOfBusDrivers(int $companyId): int;

    /**
     * Find a bus driver by their phone number.
     *
     * @param  string         $phoneNumber The phone number to search for
     * @return BusDriver|null The matching bus driver or null if not found
     */
    public function findBusDriverByPhoneNumber(string $phoneNumber): ?BusDriver;
}
