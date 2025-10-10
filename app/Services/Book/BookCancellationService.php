<?php

declare(strict_types=1);

namespace App\Services\Book;

use Exception;
use App\Enum\BookingStatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Eloquent\BusSeatRepository;
use App\Repositories\Eloquent\BusTripRepository;
use App\Repositories\Eloquent\AppSettingsRepository;
use App\Repositories\Eloquent\BusTripBookingRepository;

final readonly class BookCancellationService
{
    public function __construct(
        private BusTripRepository $busTripRepository,
        private BusTripBookingRepository $busTripBookingRepository,
        private AppSettingsRepository $appSettingsRepository,
        private BusSeatRepository $busSeatRepository,
        private BookValidationService $bookValidationService,
    ) {}

    /**
     * Cancel a booking.
     */
    public function cancelBooking(int $bookingId): void
    {
        DB::transaction(function () use ($bookingId): void {
            $busTripBooking = $this->busTripBookingRepository->findBusTripBookingById($bookingId);
            $this->bookValidationService->ensureOwnBooking($busTripBooking);
            $this->bookValidationService->validateBookingNotCanceled($busTripBooking);

            $appSetting = $this->appSettingsRepository->getAppSettingByKey('booking_cancellation_hours');
            $this->bookValidationService->assertWithinCancellationWindow($busTripBooking, $appSetting);

            $seats = $this->bookValidationService->parseSeatNumbers($busTripBooking->reserved_seat_numbers);

            $this->busSeatRepository->makeSeatsAvailable($seats, $busTripBooking->bus_trip_id);

            $trip = $this->busTripRepository->findTripById($busTripBooking->bus_trip_id);

            $this->busTripRepository->updateRemainingSeats($trip, $trip->remaining_seats + $busTripBooking->reserved_seat_count);

            $this->busTripBookingRepository->updateBusTripBooking($busTripBooking, [
                'booking_status' => BookingStatusEnum::CANCELED->value,
                'canceled_at' => Carbon::now()
            ]);
        });
    }

    /**
     * Refund a booking.
     */
    public function refundBooking(int $bookingId): void
    {
        DB::transaction(function () use ($bookingId): void {
            $busTripBooking = $this->busTripBookingRepository->findBusTripBookingById($bookingId);

            $trip = $this->busTripRepository->findTripById($busTripBooking->bus_trip_id);

            $this->bookValidationService->ensureOwnTrip($trip);

            if ($busTripBooking->booking_status !== BookingStatusEnum::CANCELED->value) {
                throw new Exception(
                    message: 'Only canceled bookings can be refunded',
                    code: Response::HTTP_BAD_REQUEST,
                );
            }

            $this->busTripBookingRepository->updateBusTripBooking($busTripBooking, [
                'booking_status' => BookingStatusEnum::REFUNDED->value
            ]);
        });
    }
}
