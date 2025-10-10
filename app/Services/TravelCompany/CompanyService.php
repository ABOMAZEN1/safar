<?php

declare(strict_types=1);

namespace App\Services\TravelCompany;

use App\Models\User;
use App\Models\TravelCompany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Eloquent\BusRepository;
use App\Repositories\Eloquent\BusTripRepository;
use App\Repositories\Eloquent\BusDriverRepository;
use App\Repositories\Eloquent\TravelCompanyRepository;
use App\Repositories\Eloquent\BusTripBookingRepository;
use App\DataTransferObjects\TravelCompany\CompanyFilterDTO;

final readonly class CompanyService
{
    public function __construct(
        private TravelCompanyRepository $travelCompanyRepository,
        private BusTripBookingRepository $busTripBookingRepository,
        private BusTripRepository $busTripRepository,
        private BusDriverRepository $busDriverRepository,
        private BusRepository $busRepository,
    ) {}

    /**
     * Get active travel companies with optional filtering.
     *
     * @param CompanyFilterDTO|null $companyFilterDTO Optional filter criteria
     * @return Collection<int, TravelCompany> Collection of filtered travel companies
     */
    public function getActiveTravelCompanies(?CompanyFilterDTO $companyFilterDTO = null): Collection
    {
        return $this->travelCompanyRepository->getActiveTravelCompanies($companyFilterDTO);
    }

    /**
     * Get a specific travel company by ID.
     *
     * @param int $id The ID of the travel company
     */
    public function getTravelCompany(int $id): TravelCompany
    {
        return $this->travelCompanyRepository->findTravelCompanyByIdOrFail($id);
    }

    public function getCompanyStatistics(): array
    {
        /**
         * @var null|User $user
         */
        $user = Auth::user();

        $company = $user->company;

        $numberOfReservations = $this->busTripBookingRepository->getNumberOfBusTripBookings($company->id);
        $numberOfCompletedTrips = $this->busTripRepository->getNumberOfCompletedTrips($company->id);
        $numberOfDrivers = $this->busDriverRepository->getNumberOfBusDrivers($company->id);
        $numberOfBuses = $this->busRepository->getNumberOfBuses($company->id);

        return [
            'number_of_reservations' => $numberOfReservations,
            'number_of_completed_trips' => $numberOfCompletedTrips,
            'number_of_drivers' => $numberOfDrivers,
            'number_of_buses' => $numberOfBuses,
        ];
    }
}
