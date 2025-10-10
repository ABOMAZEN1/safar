<?php

declare(strict_types=1);

namespace App\Services\Book;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use function sprintf;
use App\Models\BusTrip;
use App\Models\BusTripBooking;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Eloquent\BusTripRepository;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\BusTripBookingRepository;
use App\DataTransferObjects\BusTripBooking\CreateBookBusTripBookingDto;
use App\DataTransferObjects\BusTripBooking\CreateCompanyTripBookingDto;
use App\DataTransferObjects\BusTripBooking\CreateBusTripBookingByPhoneDto;
use App\Enum\UserTypeEnum;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Customer;

final readonly class BookService
{
    public function __construct(
        private BusTripRepository $busTripRepository,
        private BookCreationService $bookCreationService,
        private BookCancellationService $bookCancellationService,
        private BookQueryService $bookQueryService,
        private BookConfirmationService $bookConfirmationService,
        private BookValidationService $bookValidationService,
        private CustomerRepository $customerRepository,
    ) {}

    public function book(CreateBookBusTripBookingDto $createBookBusTripBookingDto): BusTripBooking
    {
        return DB::transaction(function () use ($createBookBusTripBookingDto): ?BusTripBooking {
            $trip = $this->busTripRepository->findTripByIdAndLockForUpdate($createBookBusTripBookingDto->busTripId);

            $this->validateTripConstraints($trip);

            $this->validateCompanionsMatchReservedSeats($createBookBusTripBookingDto->companions, $createBookBusTripBookingDto->reservedSeatCount);

            $this->bookValidationService->validateTripSeats($trip, $createBookBusTripBookingDto->reservedSeatCount);

            $this->validateSeatAvailability($trip->id, $createBookBusTripBookingDto->reservedSeatCount);

            $user = Auth::user();
            $customer = $user->customer;

            return $this->bookCreationService->createBookingForCustomer(
                $customer,
                $trip,
                $createBookBusTripBookingDto->reservedSeatCount,
                $createBookBusTripBookingDto->companions
            );
        });
    }

    /**
     * Validate trip constraints including company relationships and capacity.
     *
     * @param BusTrip $busTrip The trip to validate
     * @throws Exception If the trip fails validation
     */
    private function validateTripConstraints(BusTrip $busTrip): void
    {
        // Validate trip hasn't departed
        $this->bookValidationService->ensureTripNotPassed($busTrip->departure_datetime);

        $this->validateBusCapacity($busTrip);
        $this->validateDriverCompanyMatch($busTrip);
        $this->validateBusCompanyMatch($busTrip);
        $this->validateSeatsAvailability($busTrip);
    }

    /**
     * Validate that the number of seats doesn't exceed the bus capacity.
     *
     * @param BusTrip $busTrip The trip to validate
     * @throws Exception If the number of seats exceeds bus capacity
     */
    private function validateBusCapacity(BusTrip $busTrip): void
    {
        if ($busTrip->number_of_seats > $busTrip->bus->capacity) {
            throw new Exception(
                message: __('messages.errors.bus_trip.insufficient_seats'),
                code: Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Validate that the driver belongs to the same company as the trip.
     *
     * @param BusTrip $busTrip The trip to validate
     * @throws Exception If the driver is from a different company
     */
    private function validateDriverCompanyMatch(BusTrip $busTrip): void
    {
        if ($busTrip->busDriver && $busTrip->busDriver->travel_company_id !== $busTrip->travel_company_id) {
            throw new Exception(
                message: __('messages.errors.bus_trip.access_denied'),
                code: Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Validate that the bus belongs to the same company as the trip.
     *
     * @param BusTrip $busTrip The trip to validate
     * @throws Exception If the bus is from a different company
     */
    private function validateBusCompanyMatch(BusTrip $busTrip): void
    {
        if ($busTrip->bus && $busTrip->bus->travel_company_id !== $busTrip->travel_company_id) {
            throw new Exception(
                message: __('messages.errors.bus_trip.access_denied'),
                code: Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Validate that there are seats available for the trip.
     *
     * @param BusTrip $busTrip The trip to validate
     * @throws Exception If there are no seats available
     */
    private function validateSeatsAvailability(BusTrip $busTrip): void
    {
        if ($busTrip->remaining_seats <= 0) {
            throw new Exception(
                message: __('messages.errors.bus_trip.no_seats_available'),
                code: Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Validate that the companions list matches the requirements for the reserved seats.
     *
     * @param array|null $companions The companions array
     * @param int $reservedSeatCount The number of reserved seats
     * @throws Exception If the companions validation fails
     */
    private function validateCompanionsMatchReservedSeats(?array $companions, int $reservedSeatCount): void
    {
        if ($reservedSeatCount > 1 && count($companions) !== $reservedSeatCount - 1) {
            throw new Exception(
                message: __('messages.errors.booking.companions_count_mismatch', [
                    'count' => count($companions),
                    'seats' => $reservedSeatCount
                ]),
                code: Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Validate that there are enough unreserved seats in the bus_seats table.
     *
     * @param int $tripId The trip ID
     * @param int $requestedSeats The number of requested seats
     * @throws Exception If there aren't enough available seats
     */
    private function validateSeatAvailability(int $tripId, int $requestedSeats): void
    {
        $availableSeatsCount = DB::table('bus_seats')
            ->where('bus_trip_id', $tripId)
            ->where('is_reserved', false)
            ->count();

        $this->ensureEnoughSeatsAvailable($availableSeatsCount, $requestedSeats);
    }

    /**
     * Ensure that there are enough seats available for the booking.
     *
     * @param int $availableSeatsCount The number of available seats
     * @param int $requestedSeats The number of requested seats
     * @throws Exception If there aren't enough available seats
     */
    private function ensureEnoughSeatsAvailable(int $availableSeatsCount, int $requestedSeats): void
    {
        if ($availableSeatsCount < $requestedSeats) {
            throw new Exception(
                message: __('messages.errors.booking.insufficient_seats'),
                code: Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function cancel(int $bookingId): void
    {
        $this->bookCancellationService->cancelBooking($bookingId);
    }

    public function refund(int $bookingId): void
    {
        $this->bookCancellationService->refundBooking($bookingId);
    }

    public function getMyBookings(?string $status = null): Collection
    {
        return $this->bookQueryService->getMyBookings($status);
    }

    public function getDetails(int $bookingId): BusTripBooking
    {
        return $this->bookQueryService->getDetails($bookingId);
    }

    public function getTripBookings(int $tripId): Collection
    {
        return $this->bookQueryService->getTripBookings($tripId);
    }

    public function getBookingQrDetails(int $bookingId): BusTripBooking
    {
        return $this->bookQueryService->getBookingQrDetails($bookingId);
    }

    public function confirmDeparture(int $bookingId): void
    {
        $this->bookConfirmationService->confirmDeparture($bookingId);
    }

    public function confirmReturn(int $bookingId): void
    {
        $this->bookConfirmationService->confirmReturn($bookingId);
    }

    public function getTripPayments(int $tripId, ?array $statusFilter = null): Collection
    {
        return $this->bookQueryService->getTripPayments($tripId, $statusFilter);
    }

    public function calculateTotalPaidStatusPrice(Collection $bookings): float
    {
        return $this->bookQueryService->calculateTotalPaidStatusPrice($bookings);
    }

    public function calculateTotalCanceledOrRefundedStatusPrice(Collection $bookings): float
    {
        return $this->bookQueryService->calculateTotalCanceledOrRefundedStatusPrice($bookings);
    }

    /**
     * Create a booking by a travel company for a customer.
     */
    public function createCompanyBooking(CreateCompanyTripBookingDto $createCompanyTripBookingDto): BusTripBooking
    {
        return DB::transaction(function () use ($createCompanyTripBookingDto): BusTripBooking {
            $trip = $this->busTripRepository->findTripByIdAndLockForUpdate($createCompanyTripBookingDto->busTripId);

            $user = Auth::user();
            $this->validateCompanyAccess($user->company->id, $trip->travel_company_id);

            $this->bookValidationService->ensureTripNotPassed($trip->departure_datetime);

            $this->bookValidationService->validateTripSeats($trip, $createCompanyTripBookingDto->reservedSeatCount);

            $customer = $this->findOrCreateCustomer($createCompanyTripBookingDto);

            return $this->bookCreationService->createBookingForCustomer(
                $customer,
                $trip,
                $createCompanyTripBookingDto->reservedSeatCount,
                $createCompanyTripBookingDto->companions
            );
        });
    }

    /**
     * Validate that the user's company has access to the trip.
     *
     * @param int $userCompanyId The user's company ID
     * @param int $tripCompanyId The trip's travel company ID
     * @throws Exception If the company doesn't have access to the trip
     */
    private function validateCompanyAccess(int $userCompanyId, int $tripCompanyId): void
    {
        if ($userCompanyId !== $tripCompanyId) {
            throw new Exception(
                message: __('messages.errors.bus_trip.access_denied'),
                code: Response::HTTP_FORBIDDEN
            );
        }
    }

    /**
     * Create a booking by phone for an existing customer.
     */
    public function createBookingByPhone(CreateBusTripBookingByPhoneDto $createBusTripBookingByPhoneDto): BusTripBooking
    {
        return DB::transaction(function () use ($createBusTripBookingByPhoneDto): BusTripBooking {
            $trip = $this->busTripRepository->findTripByIdAndLockForUpdate($createBusTripBookingByPhoneDto->busTripId);

            $user = Auth::user();
            $this->validateCompanyAccess($user->company->id, $trip->travel_company_id);

            $this->bookValidationService->ensureTripNotPassed($trip->departure_datetime);

            $this->bookValidationService->validateTripSeats($trip, $createBusTripBookingByPhoneDto->reservedSeatCount);

            $customer = $this->findCustomerByPhoneNumber($createBusTripBookingByPhoneDto->phoneNumber);
            return $this->bookCreationService->createBookingForCustomer(
                $customer,
                $trip,
                $createBusTripBookingByPhoneDto->reservedSeatCount,
                $createBusTripBookingByPhoneDto->companions
            );
        });
    }

    /**
     * Find a customer by their phone number, throwing an exception if not found.
     *
     * @param string $phoneNumber The customer's phone number
     * @return Customer The found customer
     * @throws Exception If the customer is not found
     */
    private function findCustomerByPhoneNumber(string $phoneNumber): Customer
    {
        try {
            return $this->customerRepository->findCustomerByPhoneNumber($phoneNumber);
        } catch (ModelNotFoundException) {
            throw new Exception(
                message: __('messages.errors.auth.customer_not_found'),
                code: Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * Find an existing customer or create a new one based on DTO data.
     */
    private function findOrCreateCustomer(CreateCompanyTripBookingDto $createCompanyTripBookingDto): Customer
    {
        try {
            return $this->customerRepository->findCustomerByPhoneNumber($createCompanyTripBookingDto->phoneNumber);
        } catch (ModelNotFoundException) {
            $user = User::create([
                'name' => $createCompanyTripBookingDto->customerName,
                'phone_number' => $createCompanyTripBookingDto->phoneNumber,
                'password' => bcrypt(uniqid('', true)),
                'type' => UserTypeEnum::CUSTOMER->value,
                'verified_at' => now(),
            ]);

            return $this->customerRepository->createCustomer([
                'user_id' => $user->id,
                'national_id' => $createCompanyTripBookingDto->nationalId,
                'gender' => $createCompanyTripBookingDto->gender,
                'birth_date' => $createCompanyTripBookingDto->birthDate,
                'address' => $createCompanyTripBookingDto->address,
                'mother_name' => $createCompanyTripBookingDto->motherName,
            ]);
        }
    }
}
