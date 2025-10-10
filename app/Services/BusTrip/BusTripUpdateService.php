<?php

declare(strict_types=1);

namespace App\Services\BusTrip;

use App\Models\User;
use Exception;
use App\Models\BusTrip;
use App\Models\Bus;
use App\Models\BusDriver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\DataTransferObjects\BusTrip\UpdateBusTripDto;
use App\Repositories\Eloquent\BusTripRepository;

final readonly class BusTripUpdateService
{
    public function __construct(
        private BusTripRepository $busTripRepository,
    ) {}

    public function update(int $tripId, UpdateBusTripDto $updateBusTripDto): BusTrip
    {
        return DB::transaction(function () use ($tripId, $updateBusTripDto): ?BusTrip {
            $trip = $this->busTripRepository->findTripById($tripId);

            $this->ensureOwnTrip($trip);

            /** @var User $user */
            $user = Auth::user();
            $companyId = $user->company->id;

            if ($updateBusTripDto->busId !== null) {
                $this->validateBusOwnership($updateBusTripDto->busId, $companyId);
            }

            if ($updateBusTripDto->busDriverId !== null) {
                $this->validateDriverOwnership($updateBusTripDto->busDriverId, $companyId);
            }

            $updateData = $updateBusTripDto->toArray();

            if ($updateBusTripDto->numberOfSeats !== null) {
                $busIdToCheck = $updateBusTripDto->busId ?? $trip->bus_id;
                $this->validateBusCapacityForSeats($busIdToCheck, $updateBusTripDto->numberOfSeats);

                if ($this->isSeatCountChanging($trip->number_of_seats, $updateBusTripDto->numberOfSeats)) {
                    $remainingSeats = $this->calculateRemainingSeats($trip, $updateBusTripDto->numberOfSeats);
                    $this->validateNonNegativeRemainingSeats($remainingSeats);
                    $updateData['remaining_seats'] = $remainingSeats;
                }
            }

            $this->busTripRepository->updateBusTrip($trip, $updateData);

            return $trip;
        });
    }

    /**
     * Array-friendly wrapper to update a trip without constructing DTO at call site.
     *
     * @param array<string,mixed> $data
     */
    public function updateFromArray(int $tripId, array $data): BusTrip
    {
        $dto = UpdateBusTripDto::fromArray($data);
        return $this->update($tripId, $dto);
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

    private function isSeatCountChanging(int $currentSeats, int $newSeats): bool
    {
        return $currentSeats !== $newSeats;
    }

    private function calculateRemainingSeats(BusTrip $busTrip, int $newSeatCount): int
    {
        $seatDifference = $newSeatCount - $busTrip->number_of_seats;
        return $busTrip->remaining_seats + $seatDifference;
    }

    private function validateNonNegativeRemainingSeats(int $remainingSeats): void
    {
        if ($remainingSeats < 0) {
            throw new Exception(
                __('messages.errors.generic.validation.failed', [
                    'reason' => 'Cannot reduce seats below the number already reserved'
                ]),
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    private function ensureOwnTrip(BusTrip $busTrip): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->company->id !== $busTrip->travel_company_id) {
            throw new Exception(
                __('messages.errors.generic.unauthorized'),
                Response::HTTP_FORBIDDEN,
            );
        }
    }
}
