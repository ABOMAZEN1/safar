<?php

declare(strict_types=1);

namespace App\Services\City;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Eloquent\CityRepository;
use App\DataTransferObjects\City\CityFilterDTO;

final readonly class CityService
{
    public function __construct(
        private CityRepository $cityRepository,
    ) {}

    /**
     * Get all cities based on filter criteria.
     *
     * @param CityFilterDTO|null $cityFilterDTO DTO containing filter parameters
     * @return Collection Collection of cities
     */
    public function getAllCities(?CityFilterDTO $cityFilterDTO = null): Collection
    {
        $cityFilterDTO ??= new CityFilterDTO();

        if ($cityFilterDTO->language === null) {
            $language = Config::get('languages.default', 'en');
            $cityFilterDTO = new CityFilterDTO(
                language: $language,
            );
        }

        return $this->cityRepository->getAllCities($cityFilterDTO);
    }
}
