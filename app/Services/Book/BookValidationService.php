<?php

declare(strict_types=1);

namespace App\Services\Book;

use Exception;
use Carbon\Carbon;
use App\Models\BusTrip;
use App\Models\AppSetting;
use App\Models\BusTripBooking;
use App\Enum\BookingStatusEnum;
use App\Enum\UserTypeEnum;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final readonly class BookValidationService
{
    public function ensureOwnBooking(BusTripBooking $busTripBooking): void
    {
        $user = Auth::user();

        // Super Admin can access any booking
        if ($this->isSuperAdmin($user)) {
            return;
        }

        if ($user->customer?->id !== $busTripBooking->customer_id) {
            $cause = $user->customer?->id === null ? 'User is not a customer' : 'User does not own this booking';
            throw new Exception(
                message: __("messages.errors.booking.access_denied") . " Cause: " . $cause,
                code: Response::HTTP_FORBIDDEN,
            );
        }
    }

    public function ensureTripNotPassed(Carbon $departureDateTime): void
    {
        if ($departureDateTime->isPast()) {
            throw new Exception(
                message: __("messages.errors.booking.trip_passed"),
                code: Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function ensureOwnTrip(BusTrip $busTrip): void
    {
        $user = Auth::user();

        // Super Admin can access any trip
        if ($this->isSuperAdmin($user)) {
            return;
        }

        if ($user->company->id !== $busTrip->travel_company_id) {
            $cause = $user->company->id === null ? 'User is not associated with any company' : 'Company ID mismatch';
            throw new Exception(
                message: __("messages.errors.bus_trip.access_denied") . " Cause: " . $cause,
                code: Response::HTTP_FORBIDDEN,
            );
        }
    }

    public function validateTripSeats(BusTrip $busTrip, int $requestedSeats): void
    {
        if ($busTrip->remaining_seats < $requestedSeats) {
            if ($busTrip->remaining_seats === 0) {
                throw new Exception(
                    message: __("messages.errors.booking.no_seats_available"),
                    code: Response::HTTP_BAD_REQUEST,
                );
            }

            throw new Exception(
                message: __("messages.errors.booking.insufficient_seats"),
                code: Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function validateBookingNotCanceled(BusTripBooking $busTripBooking): void
    {
        if ($busTripBooking->booking_status === BookingStatusEnum::CANCELED->value) {
            throw new Exception(
                message: __("messages.errors.booking.booking_already_canceled"),
                code: Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function validateTripDate(BusTrip $busTrip): void
    {
        if (! $busTrip->departure_datetime->isSameDay(now())) {
            throw new Exception(
                message: __("messages.errors.booking.booking_not_for_today"),
                code: Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function validateDriverAuthorization(BusTrip $busTrip): void
    {
        $user = Auth::user();

        // Super Admin can authorize any driver
        if ($this->isSuperAdmin($user)) {
            return;
        }

        if ($busTrip->bus_driver_id !== $user->busDriver->id) {
            throw new Exception(
                message: __("messages.errors.booking.booking_not_authorized"),
                code: Response::HTTP_FORBIDDEN,
            );
        }
    }

    public function validateDepartureBoardingAlreadyConfirmed(BusTripBooking $busTripBooking): void
    {
        if ($busTripBooking->is_departure_confirmed) {
            throw new Exception(
                message: __("messages.errors.booking.boarding_already_confirmed"),
                code: Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function validateReturnBoardingAlreadyConfirmed(BusTripBooking $busTripBooking): void
    {
        if ($busTripBooking->is_return_confirmed) {
            throw new Exception(
                message: __("messages.errors.booking.boarding_already_confirmed"),
                code: Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function assertWithinCancellationWindow(BusTripBooking $busTripBooking, AppSetting $appSetting): void
    {
        $cancellationHours = (int) $appSetting->value;

        if (Carbon::parse($busTripBooking->created_at)->addHours($cancellationHours)->lessThan(now())) {
            throw new Exception(
                message: sprintf('You cannot cancel this booking because it has exceeded the %d-hour cancellation window.', $cancellationHours),
                code: Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function parseSeatNumbers(string $seatsNumbers): array
    {
        return array_map('intval', array_map('trim', explode(',', $seatsNumbers)));
    }

    /**
     * Check if the user is a Super Admin
     */
    private function isSuperAdmin($user): bool
    {
        return $user->roles()->where('role_name', UserTypeEnum::SUPER_ADMIN->value)->exists();
    }
}
