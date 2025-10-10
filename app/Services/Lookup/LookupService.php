<?php

declare(strict_types=1);

namespace App\Services\Lookup;

use Illuminate\Support\Collection;
use App\Services\BusType\BusTypeService;
use App\Services\City\CityService;
use App\Services\TravelCompany\CompanyService;

final readonly class LookupService
{
    public function __construct(
        private CompanyService $companyService,
        private BusTypeService $busTypeService,
        private CityService $cityService,
    ) {}

    /**
     * Get all lookup data needed for the frontend.
     *
     * @return array{active_companies: Collection, bus_types: Collection, cities: \Illuminate\Database\Eloquent\Collection}
     */
    public function getLookupData(): array
    {
        return [
            'active_companies' => $this->companyService->getActiveTravelCompanies(),
            'bus_types' => $this->busTypeService->getAllBusTypes(),
            'cities' => $this->cityService->getAllCities(),
        ];
    }
}
