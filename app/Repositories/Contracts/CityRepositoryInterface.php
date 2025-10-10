<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DataTransferObjects\City\CityFilterDTO;
use App\Models\City;
use Illuminate\Support\Collection;

/**
 * Interface CityRepositoryInterface.
 */
interface CityRepositoryInterface
{
    /**
     * Get all cities based on filter criteria.
     *
     * @param CityFilterDTO|null $cityFilterDTO DTO containing filter parameters
     * @return Collection<int, City> Collection of cities
     */
    public function getAllCities(?CityFilterDTO $cityFilterDTO = null): Collection;

    /**
     * Find a city by its ID.
     *
     * @param  int          $cityId   The city ID
     * @param  string|null  $language The language to use for city names (ar or en)
     * @return City|null    The city if found, null otherwise
     */
    public function findCityById(int $cityId, ?string $language = null): ?City;
}
