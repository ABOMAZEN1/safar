<?php

declare(strict_types=1);

namespace App\Services\BusTrip;

use App\Models\User;
use Exception;
use App\Models\BusTrip;
use Illuminate\Support\Collection;
use App\DataTransferObjects\BusTrip\TripFilterDTO;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\DataTransferObjects\BusTrip\CreateBusTripDto;
use App\Repositories\Eloquent\BusTripRepository;
use App\Repositories\Eloquent\CityRepository;
use App\Repositories\Eloquent\BusSeatRepository;
use App\Repositories\Eloquent\BusRepository;
use App\Repositories\Eloquent\BusDriverRepository;
use App\Repositories\Eloquent\TravelCompanyCommissionRepository;
use App\DataTransferObjects\BusTrip\UpdateBusTripDto;
use App\Services\BusTrip\BusTripUpdateService;
use App\Services\BusTrip\BusTripCreateService;

final readonly class BusTripService
{
    public function __construct(
        private BusTripRepository $busTripRepository,
        private CityRepository $cityRepository,
        private BusRepository $busRepository,
        private BusDriverRepository $busDriverRepository,
        private TravelCompanyCommissionRepository $travelCompanyCommissionRepository,
        private BusTripUpdateService $busTripUpdateService,
        private BusTripCreateService $busTripCreateService,
    ) {}

    public function tripSeats(int $tripId): int
    {
        $trip = $this->busTripRepository->findTripById($tripId);

        return $trip->remaining_seats;
    }

    public function createBusTrip(CreateBusTripDto $createBusTripDto): void
    {
        $this->busTripCreateService->create($createBusTripDto);
    }

    public function getCompanyTrips(): Collection
    {
        /** @var User $user */
        $user = Auth::user();

        $companyId = $user->company->id;

        return $this->busTripRepository->getCompanyTrips($companyId);
    }

    public function edit(int $tripId, UpdateBusTripDto $updateBusTripDto): BusTrip
    {
        return $this->busTripUpdateService->update($tripId, $updateBusTripDto);
    }

    public function getDriverTrips(): Collection
    {
        /** @var User $user */
        $user = Auth::user();

        $driver = $user->driver;

        return $this->busTripRepository->getDriverTrips($driver->id);
    }

    public function getTripDetails(int $tripId): BusTrip
    {
        $trip = $this->busTripRepository->findTripWithDetails($tripId);

        $this->ensureTravelCompanyOwnsTrip($trip);

        return $trip;
    }

    public function getCreateDetails(): array
    {
        /** @var User $user */
        $user = Auth::user();

        $company = $user->company;

        $allCities = $this->cityRepository->getAllCities();
        $buses = $this->busRepository->getBusesByCompanyId($company->id);
        $drivers = $this->busDriverRepository->getCompanyDrivers($company->id);
        $commission = $this->travelCompanyCommissionRepository->getTravelCompanyCommissionByCompanyId($company->id);

        return [
            'cities' => $allCities,
            'buses' => $buses,
            'drivers' => $drivers,
            'commission' => $commission,
        ];
    }

    public function getTrips(TripFilterDTO $tripFilterDTO): Collection
    {
        $this->validateRequiredSeats($tripFilterDTO);

        return $this->busTripRepository->getTrips($tripFilterDTO);
    }

    private function validateRequiredSeats(TripFilterDTO $tripFilterDTO): void
    {
        if ($tripFilterDTO->requiredSeats === null) {
            return;
        }

        if ($tripFilterDTO->travelCompanyId) {
            $maxCompanyBusCapacity = $this->getMaxBusCapacityForCompany($tripFilterDTO->travelCompanyId);

            if ($maxCompanyBusCapacity === null) {
                throw new Exception(
                    __('messages.errors.generic.validation.failed', [
                        'reason' => 'No buses found for the selected travel company'
                    ]),
                    Response::HTTP_BAD_REQUEST,
                );
            }

            if ($tripFilterDTO->requiredSeats > $maxCompanyBusCapacity) {
                throw new Exception(
                    __('messages.errors.generic.validation.failed', [
                        'reason' => sprintf(
                            'Number of passengers (%d) cannot exceed maximum bus capacity (%d)',
                            $tripFilterDTO->requiredSeats,
                            $maxCompanyBusCapacity
                        )
                    ]),
                    Response::HTTP_BAD_REQUEST,
                );
            }

            return;
        }

        if ($tripFilterDTO->busTypeId) {
            $maxBusTypeCapacity = $this->getMaxBusCapacityForType($tripFilterDTO->busTypeId);

            if ($maxBusTypeCapacity === null) {
                throw new Exception(
                    __('messages.errors.generic.validation.failed', [
                        'reason' => 'No buses found for the selected bus type'
                    ]),
                    Response::HTTP_BAD_REQUEST,
                );
            }

            if ($tripFilterDTO->requiredSeats > $maxBusTypeCapacity) {
                throw new Exception(
                    __('messages.errors.generic.validation.failed', [
                        'reason' => sprintf(
                            'Number of passengers (%d) cannot exceed maximum bus capacity (%d) for the selected bus type',
                            $tripFilterDTO->requiredSeats,
                            $maxBusTypeCapacity
                        )
                    ]),
                    Response::HTTP_BAD_REQUEST,
                );
            }

            return;
        }
    }

    private function getMaxBusCapacityForCompany(int $travelCompanyId): ?int
    {
        $maxCapacity = DB::table('buses')
            ->where('travel_company_id', $travelCompanyId)
            ->max('capacity');

        return $maxCapacity ?: null;
    }

    private function getMaxBusCapacityForType(int $busTypeId): ?int
    {
        $maxCapacity = DB::table('buses')
            ->where('bus_type_id', $busTypeId)
            ->max('capacity');

        return $maxCapacity ?: null;
    }

    private function ensureTravelCompanyOwnsTrip(BusTrip $busTrip): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($busTrip->travel_company_id !== $user->company->id) {
            throw new Exception(
                __('messages.errors.generic.unauthorized'),
                Response::HTTP_FORBIDDEN,
            );
        }
    }
}
