<?php

declare(strict_types=1);

namespace App\Services\BusTrip;

use App\Models\User;
use Exception;
use App\Models\Bus;
use App\Models\BusDriver;
use App\Enum\TripsTypeEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\DataTransferObjects\BusTrip\CreateBusTripDto;
use App\Repositories\Eloquent\BusTripRepository;
use App\Repositories\Eloquent\BusSeatRepository;

final readonly class BusTripCreateService
{
    public function __construct(
        private BusTripRepository $busTripRepository,
        private BusSeatRepository $busSeatRepository,
    ) {}

    public function create(CreateBusTripDto $createBusTripDto): void
    {
        $this->validateTwoWayTripRequirements($createBusTripDto);
        $this->setReturnFieldsForOneWayTrip($createBusTripDto);

        /** @var User $user */
        $user = Auth::user();
        $companyId = $user->company->id;

        $this->validateBusOwnership($createBusTripDto->busId, $companyId);
        $this->validateDriverOwnership($createBusTripDto->busDriverId, $companyId);
        $this->validateBusCapacityForSeats($createBusTripDto->busId, $createBusTripDto->numberOfSeats);

        $createBusTripDto->setTravelCompanyId($companyId);

        DB::transaction(function () use ($createBusTripDto): void {
            $busTrip = $this->busTripRepository->createBusTrip($createBusTripDto);
            $this->createBusSeats($busTrip->id, $createBusTripDto->numberOfSeats);
        });
    }

    /**
     * Array-friendly wrapper to create a trip without constructing DTO at call site.
     *
     * @param array<string,mixed> $data
     */
    public function createFromArray(array $data): void
    {
        $dto = CreateBusTripDto::fromArray($data);
        $this->create($dto);
    }

    private function validateBusOwnership(int $busId, int $companyId): void
    {
        $bus = Bus::findOrFail($busId);

        if ($bus->travel_company_id !== $companyId) {
            throw new Exception(
                __('messages.errors.generic.validation.failed', [
                    'reason' => 'The selected bus must belong to your company'
                ]),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    private function validateDriverOwnership(int $driverId, int $companyId): void
    {
        $driver = BusDriver::findOrFail($driverId);

        if ($driver->travel_company_id !== $companyId) {
            throw new Exception(
                __('messages.errors.generic.validation.failed', [
                    'reason' => 'The selected driver must belong to your company'
                ]),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    private function validateBusCapacityForSeats(int $busId, int $requestedSeats): void
    {
        $bus = Bus::findOrFail($busId);

        if ($requestedSeats > $bus->capacity) {
            throw new Exception(
                __('messages.errors.generic.validation.failed', [
                    'reason' => sprintf(
                        'The number of seats (%d) cannot exceed the bus capacity (%d)',
                        $requestedSeats,
                        $bus->capacity
                    )
                ]),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    private function validateTwoWayTripRequirements(CreateBusTripDto $createBusTripDto): void
    {
        if ($createBusTripDto->tripType !== TripsTypeEnum::TWO_WAY->value) {
            return;
        }

        if (! isset($createBusTripDto->returnDatetime)) {
            throw new Exception(
                __('messages.errors.generic.validation.required_field', ['field' => 'return date']),
                Response::HTTP_BAD_REQUEST,
            );
        }

        if (! isset($createBusTripDto->durationOfReturnTrip)) {
            throw new Exception(
                __('messages.errors.generic.validation.required_field', ['field' => 'return trip duration']),
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    private function setReturnFieldsForOneWayTrip(CreateBusTripDto $createBusTripDto): void
    {
        if ($createBusTripDto->tripType === TripsTypeEnum::ONE_WAY->value) {
            $createBusTripDto->durationOfReturnTrip = null;
            $createBusTripDto->returnDatetime = null;
        }
    }

    private function createBusSeats(int $busTripId, int $numberOfSeats): void
    {
        $seats = collect(range(1, $numberOfSeats))
            ->map(fn(int $seatNumber): array => [
                'bus_trip_id' => $busTripId,
                'seat_number' => $seatNumber,
                'is_reserved' => false,
                'created_at' => now(),
            ])
            ->all();

        $this->busSeatRepository->insertBusSeats($seats);
    }
}
