<?php

declare(strict_types=1);

namespace App\Services\Book;

use Exception;
use App\Models\User;
use App\Models\BusTrip;
use App\Models\BusTripBooking;
use App\Enum\BookingStatusEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Eloquent\BusTripRepository;
use App\Repositories\Eloquent\BusTripBookingRepository;

final readonly class BookQueryService
{
    public function __construct(
        private BusTripBookingRepository $busTripBookingRepository,
        private BusTripRepository $busTripRepository,
        private BookValidationService $bookValidationService,
    ) {}

    /**
     * Get booking QR code details.
     */
    public function getBookingQrDetails(int $bookId): BusTripBooking
    {
        return $this->busTripBookingRepository->findBookingWithQrDetails($bookId);
    }

    /**
     * Get booking details by ID.
     */
    public function getDetails(int $bookId): BusTripBooking
    {
        $busTripBooking = $this->busTripBookingRepository->findBusTripBookingById($bookId);

        /** @var User $user */
        $user = Auth::user();

        $this->validateCustomerAccessToBooking($user, $busTripBooking);
        $this->validateCompanyAccessToBooking($user, $busTripBooking);

        return $busTripBooking;
    }

    /**
     * Get bookings for the authenticated user.
     *
     * @return Collection<int, BusTripBooking>
     */
    public function getMyBookings(?string $status = null): Collection
    {
        /** @var User $user */
        $user = Auth::user();
        $customerId = $this->validateAndGetCustomerId($user);

        return $this->busTripBookingRepository->getCustomerBookings($customerId, $status);
    }

    /**
     * Get all bookings for a specific trip.
     */
    public function getTripBookings(int $tripId): Collection
    {
        $trip = $this->busTripRepository->findTripById($tripId);

        /** @var User $user */
        $user = Auth::user();

        $this->validateCompanyAccessToTrip($user, $trip);

        return $this->busTripBookingRepository->getTripBookings($tripId);
    }

    /**
     * Get trip payments by status.
     */
    public function getTripPayments(int $tripId, ?array $statusFilter = null): Collection
    {
        $trip = $this->busTripRepository->findTripById($tripId);
        $this->bookValidationService->ensureOwnTrip($trip);

        if ($statusFilter === null) {
            return $this->busTripBookingRepository->getBusTripPayments($tripId);
        }

        return $this->busTripBookingRepository->getBusTripBookingsByTripIdAndStatus($tripId, $statusFilter);
    }

    /**
     * Calculate total paid status price.
     */
    public function calculateTotalPaidStatusPrice(Collection $bookings): float
    {
        return $bookings->where('booking_status', BookingStatusEnum::PAID->value)
            ->sum('total_price');
    }

    /**
     * Calculate total canceled or refunded status price.
     */
    public function calculateTotalCanceledOrRefundedStatusPrice(Collection $bookings): float
    {
        return $bookings->whereIn('booking_status', [
            BookingStatusEnum::CANCELED->value,
            BookingStatusEnum::REFUNDED->value,
        ])
            ->sum('total_price');
    }

    /**
     * Validate that a customer has access to the booking.
     */
    private function validateCustomerAccessToBooking(User $user, BusTripBooking $busTripBooking): void
    {
        // If the user is a customer, ensure they own the booking
        if ($user->customer && $user->customer->id !== $busTripBooking->customer_id) {
            throw new Exception(
                message: __('messages.errors.booking.access_denied') . ' - Cause: User does not own the booking',
                code: Response::HTTP_FORBIDDEN,
            );
        }
    }

    /**
     * Validate that a travel company has access to the booking.
     */
    private function validateCompanyAccessToBooking(User $user, BusTripBooking $busTripBooking): void
    {
        // If the user is from a travel company, ensure the booking is for a trip managed by their company
        if ($user->company) {
            $trip = $this->busTripRepository->findTripById($busTripBooking->bus_trip_id);

            if ($trip->travel_company_id !== $user->company->id) {
                $cause = 'Travel company does not own the trip associated with this booking';

                throw new Exception(
                    message: __('messages.errors.booking.access_denied') . ' - Cause: ' . $cause,
                    code: Response::HTTP_FORBIDDEN,
                );
            }
        }
    }

    /**
     * Validate that a travel company has access to the trip.
     */
    private function validateCompanyAccessToTrip(User $user, BusTrip $busTrip): void
    {
        if ($user->company && $user->company->id !== $busTrip->travel_company_id) {
            $cause = 'User does not belong to the travel company associated with this trip';

            throw new Exception(
                message: __('messages.errors.bus_trip.access_denied') . ' - Cause: ' . $cause,
                code: Response::HTTP_FORBIDDEN,
            );
        }
    }

    /**
     * Validate and get customer ID.
     */
    private function validateAndGetCustomerId(User $user): int
    {
        $customerId = $user->customer?->id;

        if (!$customerId) {
            throw new Exception(
                message: 'You must be a customer to view bookings',
                code: Response::HTTP_FORBIDDEN,
            );
        }

        return $customerId;
    }
}
