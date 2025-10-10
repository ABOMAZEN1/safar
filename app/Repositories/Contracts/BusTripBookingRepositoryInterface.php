<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\BusTripBooking;
use Illuminate\Support\Collection;

interface BusTripBookingRepositoryInterface
{
    /**
     * Create a new bus trip booking.
     *
     * @param  array<string, mixed> $data The booking data
     * @return BusTripBooking       The created booking
     */
    public function createBusTripBooking(array $data): BusTripBooking;

    /**
     * Update an existing bus trip booking.
     *
     * @param BusTripBooking       $busTripBooking           The booking to update
     * @param array<string, mixed> $updateBusTripBookingData The data to update
     */
    public function updateBusTripBooking(BusTripBooking $busTripBooking, array $updateBusTripBookingData): void;

    /**
     * Get a bus trip booking by its ID with related data.
     *
     * @param  int                 $bookId The booking ID
     * @return BusTripBooking|null The booking with relations if found
     */
    public function findBookingWithQrDetails(int $bookId): ?BusTripBooking;

    /**
     * Find a bus trip booking by its ID.
     *
     * @param  int                 $bookingId The booking ID
     * @return BusTripBooking|null The booking if found
     */
    public function findBusTripBookingById(int $bookingId): ?BusTripBooking;

    /**
     * Get upcoming bus trip bookings for a customer.
     *
     * @param  int                             $customerId The customer ID
     * @return Collection<int, BusTripBooking> Collection of upcoming bookings
     */
    public function getUpcomingBusTripBookings(int $customerId): Collection;

    /**
     * Get passed bookings for a customer.
     *
     * @param  int                             $customerId The customer ID
     * @return Collection<int, BusTripBooking> Collection of passed bookings
     */
    public function getPassedBookings(int $customerId): Collection;

    /**
     * Get all bookings for a customer.
     *
     * @param  int                             $customerId The customer ID
     * @return Collection<int, BusTripBooking> Collection of all customer bookings
     */
    public function getUserBookings(int $customerId): Collection;

    /**
     * Get details of a bus trip booking by its ID.
     *
     * @param  int                 $bookId The booking ID
     * @return BusTripBooking|null The booking details if found
     */
    public function getBusTripBookingDetails(int $bookId): ?BusTripBooking;

    /**
     * Get all bus trip bookings for a company.
     *
     * @param  int                             $companyId The company ID
     * @return Collection<int, BusTripBooking> Collection of company bookings
     */
    public function getCompanyBusTripBookings(int $companyId): Collection;

    /**
     * Get bookings for a specific trip.
     *
     * @param  int                             $busTripId The bus trip ID
     * @return Collection<int, BusTripBooking> Collection of trip bookings
     */
    public function getTripBookings(int $busTripId): Collection;

    /**
     * Get the latest bus trip booking for a customer.
     *
     * @param  int                 $customerId The customer ID
     * @return BusTripBooking|null The latest booking if exists
     */
    public function getLatestBusTripBooking(int $customerId): ?BusTripBooking;

    /**
     * Get payments for a specific bus trip.
     *
     * @param  int                             $busTripId The bus trip ID
     * @return Collection<int, BusTripBooking> Collection of trip payments
     */
    public function getBusTripPayments(int $busTripId): Collection;

    /**
     * Refund a bus trip booking by its ID.
     *
     * @param int $bookingId The booking ID
     */
    public function refundBusTripBooking(int $bookingId): void;

    /**
     * Get the number of bus trip bookings for a company.
     *
     * @param  int $companyId The company ID
     * @return int The number of bookings
     */
    public function getNumberOfBusTripBookings(int $companyId): int;
}
