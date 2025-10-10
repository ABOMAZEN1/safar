<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\BusTripBooking;
use App\Models\BusTrip;
use App\Models\BusSeat;
use Exception;
use App\Enum\BusTripStatusEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class BusTripBookingObserver
{
    /**
     * Handle the BusTripBooking "creating" event.
     */
    public function creating(BusTripBooking $busTripBooking): void
    {
        Log::info('BusTripBookingObserver: Starting validation for new booking', [
            'bus_trip_id' => $busTripBooking->bus_trip_id,
            'reserved_seat_count' => $busTripBooking->reserved_seat_count,
            'reserved_seat_numbers' => $busTripBooking->reserved_seat_numbers
        ]);

        $busTrip = $this->findAndValidateBusTrip($busTripBooking->bus_trip_id);
        $this->validateSeatAvailability($busTrip, $busTripBooking);
        $this->validateSeatNumbers($busTrip, $busTripBooking);
    }

    /**
     * Handle the BusTripBooking "updated" event.
     */
    public function updated(BusTripBooking $busTripBooking): void
    {
        $this->restoreSeatsIfBookingCancelled($busTripBooking);
    }

    /**
     * Handle the BusTripBooking "deleted" event.
     */
    public function deleted(BusTripBooking $busTripBooking): void
    {
        $this->restoreSeatsForDeletedBooking($busTripBooking);
    }

    /**
     * Find and validate the bus trip exists.
     *
     * @param int $tripId The trip ID to find
     * @return BusTrip The validated bus trip
     * @throws Exception If the bus trip does not exist
     */
    private function findAndValidateBusTrip(int $tripId): BusTrip
    {
        try {
            /** @var BusTrip $busTrip */
            $busTrip = BusTrip::findOrFail($tripId);

            return $busTrip;
        } catch (ModelNotFoundException) {
            Log::error('BusTripBooking validation failed: Trip not found', [
                'bus_trip_id' => $tripId,
                'error' => __('messages.errors.bus_trip.not_found'),
                'resolution' => 'Select a valid bus trip ID that exists in the database.'
            ]);

            throw new Exception(__('messages.errors.bus_trip.not_found'));
        }
    }

    /**
     * Validate that the trip has enough remaining seats.
     *
     * @param BusTrip $busTrip The bus trip to validate
     * @param BusTripBooking $busTripBooking The booking to validate
     * @throws Exception If there are not enough seats available
     */
    private function validateSeatAvailability(BusTrip $busTrip, BusTripBooking $busTripBooking): void
    {
        if ($busTrip->remaining_seats < $busTripBooking->reserved_seat_count) {
            $errorMessage = __('messages.errors.bus_trip.insufficient_seats');

            Log::error('BusTripBooking validation failed: Insufficient seats', [
                'trip_id' => $busTrip->id,
                'requested_seats' => $busTripBooking->reserved_seat_count,
                'available_seats' => $busTrip->remaining_seats,
                'error' => $errorMessage,
                'resolution' => 'Either reduce the number of requested seats or select a different trip with more available seats.'
            ]);

            throw new Exception($errorMessage);
        }
    }

    /**
     * Validate seat numbers if provided.
     *
     * @param BusTrip $busTrip The bus trip to validate
     * @param BusTripBooking $busTripBooking The booking to validate
     * @throws Exception If seat validation fails
     */
    private function validateSeatNumbers(BusTrip $busTrip, BusTripBooking $busTripBooking): void
    {
        if (empty($busTripBooking->reserved_seat_numbers)) {
            return;
        }

        $seatNumbers = explode(',', $busTripBooking->reserved_seat_numbers);

        Log::info('BusTripBookingObserver: Validating seat numbers', [
            'seat_numbers' => $seatNumbers,
            'count' => count($seatNumbers),
            'reserved_count' => $busTripBooking->reserved_seat_count
        ]);

        $this->validateSeatCount($seatNumbers, $busTripBooking);
        $this->validateSeatValidity($seatNumbers, $busTrip);
        $this->validateSeatAvailabilityStatus($seatNumbers, $busTrip);
    }

    /**
     * Validate that the number of seat numbers matches the reserved seat count.
     *
     * @param array $seatNumbers Array of seat numbers
     * @param BusTripBooking $busTripBooking The booking to validate
     * @throws Exception If the seat count doesn't match
     */
    private function validateSeatCount(array $seatNumbers, BusTripBooking $busTripBooking): void
    {
        if (count($seatNumbers) !== $busTripBooking->reserved_seat_count) {
            $errorMessage = __('messages.errors.booking.seat_count_mismatch', [
                'provided' => count($seatNumbers),
                'reserved' => $busTripBooking->reserved_seat_count
            ]);

            Log::error('BusTripBooking validation failed: Seat count mismatch', [
                'seat_numbers_count' => count($seatNumbers),
                'reserved_seat_count' => $busTripBooking->reserved_seat_count,
                'error' => $errorMessage,
                'resolution' => 'Ensure the number of specified seat numbers matches the reserved seat count.'
            ]);

            throw new Exception($errorMessage);
        }
    }

    /**
     * Validate that all seat numbers are valid.
     *
     * @param array $seatNumbers Array of seat numbers
     * @param BusTrip $busTrip The bus trip to validate
     * @throws Exception If any seat number is invalid
     */
    private function validateSeatValidity(array $seatNumbers, BusTrip $busTrip): void
    {
        foreach ($seatNumbers as $seatNumber) {
            if (!is_numeric($seatNumber)) {
                $errorMessage = __('messages.errors.booking.invalid_seat_number', [
                    'seat' => $seatNumber
                ]);

                Log::error('BusTripBooking validation failed: Invalid seat number', [
                    'seat_number' => $seatNumber,
                    'error' => $errorMessage,
                    'resolution' => 'Use only numeric values for seat numbers.'
                ]);

                throw new Exception($errorMessage);
            }

            $seatNum = (int) $seatNumber;

            if ($seatNum <= 0) {
                $errorMessage = __('messages.errors.booking.non_positive_seat_number');

                Log::error('BusTripBooking validation failed: Non-positive seat number', [
                    'seat_number' => $seatNum,
                    'error' => $errorMessage,
                    'resolution' => 'Use only positive integers for seat numbers.'
                ]);

                throw new Exception($errorMessage);
            }

            if ($seatNum > $busTrip->number_of_seats) {
                $errorMessage = __('messages.errors.booking.seat_exceeds_total', [
                    'seat' => $seatNum,
                    'total' => $busTrip->number_of_seats
                ]);

                Log::error('BusTripBooking validation failed: Seat number exceeds total seats', [
                    'seat_number' => $seatNum,
                    'total_seats' => $busTrip->number_of_seats,
                    'trip_id' => $busTrip->id,
                    'error' => $errorMessage,
                    'resolution' => 'Select seat numbers within the valid range for this bus.'
                ]);

                throw new Exception($errorMessage);
            }

            // Check if seat exists in the bus_seats table
            $seat = BusSeat::where('bus_trip_id', $busTrip->id)
                ->where('seat_number', $seatNum)
                ->firstOrFail();

            if (!$seat) {
                $errorMessage = __('messages.errors.booking.seat_not_exist', [
                    'seat' => $seatNum
                ]);

                Log::error('BusTripBooking validation failed: Seat does not exist', [
                    'seat_number' => $seatNum,
                    'bus_trip_id' => $busTrip->id,
                    'error' => $errorMessage,
                    'resolution' => 'Select seat numbers that exist for this bus trip.'
                ]);

                throw new Exception($errorMessage);
            }

            // Check if the seat is marked as reserved in the bus_seats table
            if ($seat->is_reserved) {
                Log::error('BusTripBooking validation warning: Seat is already marked as reserved in bus_seats table', [
                    'seat_number' => $seatNum,
                    'bus_trip_id' => $busTrip->id,
                    'resolution' => 'This seat might be double-booked if multiple bookings are processed simultaneously.'
                ]);
            }
        }
    }

    /**
     * Validate that no seats are already taken in other bookings.
     *
     * @param array $seatNumbers Array of seat numbers
     * @param BusTrip $busTrip The bus trip to validate
     * @throws Exception If any seat is already taken
     */
    private function validateSeatAvailabilityStatus(array $seatNumbers, BusTrip $busTrip): void
    {
        $existingBookings = BusTripBooking::where('bus_trip_id', $busTrip->id)
            ->where('booking_status', '!=', BusTripStatusEnum::CANCELED->value)
            ->get();

        $takenSeats = [];
        foreach ($existingBookings as $existingBooking) {
            if (empty($existingBooking->reserved_seat_numbers)) {
                continue;
            }

            $bookingSeats = explode(',', (string) $existingBooking->reserved_seat_numbers);
            Log::info('BusTripBookingObserver: Existing booking seats', [
                'booking_id' => $existingBooking->id,
                'seats' => $bookingSeats
            ]);
            $takenSeats = array_merge($takenSeats, $bookingSeats);
        }

        Log::info('BusTripBookingObserver: All taken seats from bookings', [
            'taken_seats' => $takenSeats
        ]);

        $takenSeats = array_map('intval', $takenSeats);
        $requestedSeats = array_map('intval', $seatNumbers);

        $duplicateSeats = array_intersect($requestedSeats, $takenSeats);

        if ($duplicateSeats !== []) {
            $errorMessage = __('messages.errors.booking.seats_already_taken', [
                'seats' => implode(', ', $duplicateSeats)
            ]);

            Log::error('BusTripBooking validation failed: Duplicate seats detected', [
                'duplicate_seats' => $duplicateSeats,
                'requested_seats' => $requestedSeats,
                'taken_seats' => $takenSeats,
                'error' => $errorMessage,
                'resolution' => 'Select different seats that are not already reserved by other bookings.'
            ]);

            throw new Exception($errorMessage);
        }

        Log::info('BusTripBookingObserver: Seat validation passed', [
            'reserved_seats' => $requestedSeats
        ]);
    }

    /**
     * Handle when a booking status is changed to CANCELED.
     *
     * @param BusTripBooking $busTripBooking The booking that was updated
     */
    private function restoreSeatsIfBookingCancelled(BusTripBooking $busTripBooking): void
    {
        $isNewlyCancelled =
            $busTripBooking->wasChanged('booking_status') &&
            $busTripBooking->booking_status === BusTripStatusEnum::CANCELED->value &&
            $busTripBooking->getOriginal('booking_status') !== BusTripStatusEnum::CANCELED->value;

        if ($isNewlyCancelled) {
            Log::info('BusTripBookingObserver: Booking status changed to CANCELED', [
                'booking_id' => $busTripBooking->id,
                'seat_count' => $busTripBooking->reserved_seat_count
            ]);

            $this->updateTripRemainingSeats($busTripBooking);
        }
    }

    /**
     * Handle when a booking is deleted.
     *
     * @param BusTripBooking $busTripBooking The booking that was deleted
     */
    private function restoreSeatsForDeletedBooking(BusTripBooking $busTripBooking): void
    {
        $isActiveBooking = $busTripBooking->booking_status !== BusTripStatusEnum::CANCELED->value;

        if ($isActiveBooking) {
            Log::info('BusTripBookingObserver: Booking deleted, restoring seats', [
                'booking_id' => $busTripBooking->id,
                'seat_count' => $busTripBooking->reserved_seat_count
            ]);

            $this->updateTripRemainingSeats($busTripBooking);
        }
    }

    /**
     * Update trip's remaining seats and release seat reservations.
     */
    private function updateTripRemainingSeats(BusTripBooking $busTripBooking): void
    {
        $busTrip = BusTrip::find($busTripBooking->bus_trip_id);

        if ($busTrip) {
            $busTrip->remaining_seats += $busTripBooking->reserved_seat_count;
            $busTrip->save();

            $this->releaseReservedSeats($busTripBooking);
        }
    }

    /**
     * Release seat reservations in the bus_seats table.
     *
     * @param BusTripBooking $busTripBooking The booking with seats to release
     */
    private function releaseReservedSeats(BusTripBooking $busTripBooking): void
    {
        if (!empty($busTripBooking->reserved_seat_numbers)) {
            $seatNumbers = explode(',', $busTripBooking->reserved_seat_numbers);
            $seatNumbers = array_map('intval', $seatNumbers);

            BusSeat::where('bus_trip_id', $busTripBooking->bus_trip_id)
                ->whereIn('seat_number', $seatNumbers)
                ->update(['is_reserved' => false]);

            Log::info('BusTripBookingObserver: Released seats marked as available', [
                'seat_numbers' => $seatNumbers
            ]);
        }
    }
}
