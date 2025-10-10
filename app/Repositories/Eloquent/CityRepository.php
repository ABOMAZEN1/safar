<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\DataTransferObjects\City\CityFilterDTO;
use App\Models\City;
use Illuminate\Support\Collection;

/**
 * Class CityRepository.
 *
 * Handles the retrieval and management of cities.
 */
final readonly class CityRepository
{
    public function __construct(
        private City $city,
    ) {}

    /**
     * Get all cities based on filter criteria.
     *
     * @param CityFilterDTO|null $cityFilterDTO DTO containing filter parameters
     * @return Collection<int, City> Collection of cities
     */
    public function getAllCities(?CityFilterDTO $cityFilterDTO = null): Collection
    {
        $cityFilterDTO ??= new CityFilterDTO();

        if ($cityFilterDTO->language) {
            app()->setLocale($cityFilterDTO->language);
        }

        $query = $this->city;

        return $query->orderBy('name_' . app()->getLocale())->get();
    }
}
