<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\BusTripBooking;
use App\Enum\BookingStatusEnum;
use App\Enum\UserBookingsStatus;
use Illuminate\Support\Collection;

final readonly class BusTripBookingRepository
{
    public function __construct(
        private BusTripBooking $busTripBooking,
    ) {}

    /**
     * Get the default relations for eager loading.
     */
    private function defaultRelations(): array
    {
        return [
            'busTrip',
            'busTrip.travelCompany',
            'busTrip.fromCity',
            'busTrip.toCity',
        ];
    }

    /**
     * Get the detailed relations for QR and detailed views.
     */
    private function detailedRelations(): array
    {
        return [
            'customer',
            'customer.user',
            'busTrip',
            'busTrip.travelCompany',
            'busTrip.bus',
            'busTrip.busDriver',
            'busTrip.busDriver.user',
            'busTrip.fromCity',
            'busTrip.toCity',
        ];
    }

    /**
     * Create a new bus trip booking.
     *
     * @param  array<string, mixed> $data The booking data
     * @return BusTripBooking       The created booking
     */
    public function createBusTripBooking(array $data): BusTripBooking
    {
        $busTripBooking = new BusTripBooking();
        $busTripBooking->busTrip()->associate($data['bus_trip_id']);
        $busTripBooking->customer()->associate($data['customer_id']);
        $busTripBooking->fill($data);
        $busTripBooking->save();

        return $busTripBooking->load([
            'busTrip',
            'busTrip.travelCompany',
        ]);
    }

    /**
     * Update a bus trip booking.
     *
     * @param BusTripBooking       $busTripBooking The booking to update
     * @param array<string, mixed> $data           The data to update
     */
    public function updateBusTripBooking(BusTripBooking $busTripBooking, array $data): void
    {
        $busTripBooking->update($data);
    }

    /**
     * Show a bus trip booking by its ID with QR details.
     *
     * @param  int            $bookId The booking ID
     * @return BusTripBooking The booking with all relations
     */
    public function findBookingWithQrDetails(int $bookId): BusTripBooking
    {
        return $this->busTripBooking
            ->with($this->detailedRelations())
            ->findOrFail($bookId);
    }

    /**
     * Find a bus trip booking by its ID.
     *
     * @param  int            $bookingId The booking ID
     * @return BusTripBooking The booking with basic relations
     */
    public function findBusTripBookingById(int $bookingId): BusTripBooking
    {
        return $this->busTripBooking
            ->with(['busTrip'])
            ->findOrFail($bookingId);
    }

    /**
     * Get customer bookings with optional status filter.
     *
     * @param  int                             $customerId  The customer ID
     * @param  string|null                     $statusFilter Optional status filter ('upcoming', 'passed', or null for all)
     * @return Collection<int, BusTripBooking> Collection of bookings
     */
    public function getCustomerBookings(int $customerId, ?string $statusFilter = null): Collection
    {
        $query = $this->busTripBooking
            ->with($this->defaultRelations())
            ->forCustomer($customerId);

        if ($statusFilter === UserBookingsStatus::UPCOMING->value) {
            $query->upcoming();
        } elseif ($statusFilter === UserBookingsStatus::PASSED->value) {
            $query->passed();
        }

        return $query->orderByDesc('id')->get();
    }

    /**
     * Get upcoming bus trip bookings for a customer.
     *
     * @param  int                             $customerId The customer ID
     * @return Collection<int, BusTripBooking> Collection of upcoming bookings
     */
    public function getUpcomingBusTripBookings(int $customerId): Collection
    {
        return $this->getCustomerBookings($customerId, UserBookingsStatus::UPCOMING->value);
    }

    /**
     * Get passed bookings for a customer.
     *
     * @param  int                             $customerId The customer ID
     * @return Collection<int, BusTripBooking> Collection of passed bookings
     */
    public function getPassedBookings(int $customerId): Collection
    {
        return $this->getCustomerBookings($customerId, UserBookingsStatus::PASSED->value);
    }

    /**
     * Get all bookings for a customer.
     *
     * @param  int                             $customerId The customer ID
     * @return Collection<int, BusTripBooking> Collection of all bookings
     */
    public function getUserBookings(int $customerId): Collection
    {
        return $this->getCustomerBookings($customerId, UserBookingsStatus::ALL->value);
    }

    /**
     * Get the details of a bus trip booking.
     *
     * @param  int            $bookId The booking ID
     * @return BusTripBooking The booking details
     */
    public function getBusTripBookingDetails(int $bookId): BusTripBooking
    {
        return $this->busTripBooking
            ->with($this->defaultRelations())
            ->findOrFail($bookId);
    }

    /**
     * Get all bookings for a specific company.
     *
     * @param  int                             $companyId The company ID
     * @return Collection<int, BusTripBooking> Collection of company bookings
     */
    public function getCompanyBusTripBookings(int $companyId): Collection
    {
        return $this->busTripBooking
            ->with($this->defaultRelations())
            ->forCompany($companyId)
            ->get();
    }

    /**
     * Get bookings for a specific company and optionally a specific trip.
     *
     * @param  int                             $companyId The company ID
     * @param  int|null                        $tripId    The trip ID
     * @return Collection<int, BusTripBooking> Collection of bookings
     */
    public function getByCompanyId(int $companyId, ?int $tripId = null): Collection
    {
        return $this->busTripBooking
            ->forCompany($companyId)
            ->when($tripId !== null, fn($query) => $query->forTrip($tripId))
            ->with([
                'busTrip',
                'busTrip.bus',
                'customer',
                'customer.user',
            ])
            ->get();
    }

    /**
     * Get all bookings for a specific trip.
     *
     * @param  int                             $tripId The trip ID
     * @return Collection<int, BusTripBooking> Collection of trip bookings
     */
    public function getTripBookings(int $tripId): Collection
    {
        return $this->busTripBooking
            ->with([
                'customer',
                'customer.user',
                'companions',
            ])
            ->forTrip($tripId)
            ->active()
            ->get();
    }

    /**
     * Get the latest booking for a specific customer.
     *
     * @param  int            $customerId The customer ID
     * @return BusTripBooking The latest booking
     */
    public function getLatestBusTripBooking(int $customerId): BusTripBooking
    {
        return $this->busTripBooking
            ->with([
                'busTrip',
                'busTrip.travelCompany',
            ])
            ->forCustomer($customerId)
            ->latest()
            ->firstOrFail();
    }

    /**
     * Get all payments for a specific trip.
     *
     * @param  int                             $tripId The trip ID
     * @return Collection<int, BusTripBooking> Collection of trip payments
     */
    public function getBusTripPayments(int $tripId): Collection
    {
        return $this->busTripBooking
            ->with([
                'customer',
                'customer.user',
            ])
            ->forTrip($tripId)
            ->get();
    }

    /**
     * Get trip bookings by status.
     *
     * @param  int                             $tripId      The trip ID
     * @param  array<string>|null              $statusFilter Array of status values to filter by
     * @return Collection<int, BusTripBooking> Collection of filtered trip bookings
     */
    public function getBusTripBookingsByTripIdAndStatus(int $tripId, ?array $statusFilter = null): Collection
    {
        return $this->busTripBooking
            ->with([
                'customer',
                'customer.user',
            ])
            ->forTrip($tripId)
            ->when($statusFilter !== null, fn($query) => $query->withStatus($statusFilter))
            ->get();
    }

    /**
     * Refund a specific bus trip booking.
     *
     * @param int $bookingId The booking ID
     */
    public function refundBusTripBooking(int $bookingId): void
    {
        $this->busTripBooking
            ->where('id', $bookingId)
            ->withStatus(BookingStatusEnum::CANCELED->value)
            ->update(['booking_status' => BookingStatusEnum::REFUNDED->value]);
    }

    /**
     * Get the number of bookings for a specific company.
     *
     * @param  int $companyId The company ID
     * @return int The number of bookings
     */
    public function getNumberOfBusTripBookings(int $companyId): int
    {
        return $this->busTripBooking
            ->withStatus(BookingStatusEnum::PAID->value)
            ->forCompany($companyId)
            ->count();
    }
}
