<?php

declare(strict_types=1);

namespace App\Services\Book;

use Exception;
use App\Models\BusTripBooking;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Eloquent\BusTripRepository;
use App\Repositories\Eloquent\BusTripBookingRepository;

final readonly class BookConfirmationService
{
    public function __construct(
        private BusTripBookingRepository $busTripBookingRepository,
        private BusTripRepository $busTripRepository,
        private BookValidationService $bookValidationService,
    ) {}

    /**
     * Confirm departure boarding.
     */
    public function confirmDeparture(int $bookingId): void
    {
        DB::transaction(function () use ($bookingId): void {
            $busTripBooking = $this->busTripBookingRepository->findBusTripBookingById($bookingId);

            $this->validateConfirmDeparture($busTripBooking);

            $this->busTripBookingRepository->updateBusTripBooking($busTripBooking, [
                'is_departure_confirmed' => true,
            ]);
        });
    }

    /**
     * Confirm return boarding.
     */
    public function confirmReturn(int $bookingId): void
    {
        DB::transaction(function () use ($bookingId): void {
            $busTripBooking = $this->busTripBookingRepository->findBusTripBookingById($bookingId);

            $this->validateConfirmReturn($busTripBooking);

            $this->busTripBookingRepository->updateBusTripBooking($busTripBooking, [
                'is_return_confirmed' => true,
            ]);
        });
    }

    /**
     * Validate that a departure confirmation can be made.
     */
    private function validateConfirmDeparture(BusTripBooking $busTripBooking): void
    {
        $trip = $this->busTripRepository->findTripById($busTripBooking->bus_trip_id);

        $this->bookValidationService->validateTripDate($trip);

        $this->bookValidationService->validateDriverAuthorization($trip);

        $this->bookValidationService->validateDepartureBoardingAlreadyConfirmed($busTripBooking);
    }

    /**
     * Validate that a return confirmation can be made.
     */
    private function validateConfirmReturn(BusTripBooking $busTripBooking): void
    {
        $trip = $this->busTripRepository->findTripById($busTripBooking->bus_trip_id);

        $this->bookValidationService->validateTripDate($trip);

        $this->bookValidationService->validateDriverAuthorization($trip);

        if (!$busTripBooking->is_departure_confirmed) {
            throw new Exception(
                message: 'Departure boarding must be confirmed before return boarding',
                code: Response::HTTP_BAD_REQUEST,
            );
        }

        $this->bookValidationService->validateReturnBoardingAlreadyConfirmed($busTripBooking);
    }
}
