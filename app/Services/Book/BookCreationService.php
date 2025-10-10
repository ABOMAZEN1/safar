<?php

declare(strict_types=1);

namespace App\Services\Book;

use Exception;
use function count;
use function sprintf;
use App\Models\BusTrip;
use App\Models\BusTripBooking;
use App\Enum\BookingStatusEnum;
use App\Jobs\GenerateQrCodeJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Contracts\Routing\UrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Eloquent\BusSeatRepository;
use App\Repositories\Eloquent\BusTripRepository;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Repositories\Eloquent\BusTripBookingRepository;
use App\Repositories\Eloquent\TravelCompanionRepository;

final readonly class BookCreationService
{







    /**
     * Error message constants
     */
    private const ERROR_COMPANION_COUNT = 'The number of companions (%d) must be exactly one less than the number of reserved seats (%d).';
    
    private const ERROR_NO_SEATS = 'No seats are available for this trip.';
    
    private const ERROR_INSUFFICIENT_SEATS = 'Could not reserve %d seats. Only %d seats are actually available.';
    
    private const ERROR_SEAT_MISMATCH = 'Could not reserve the requested number of seats. Requested: %d, Available: %d.';

    public function __construct(
        private BusTripRepository $busTripRepository,
        private BusTripBookingRepository $busTripBookingRepository,
        private BusSeatRepository $busSeatRepository,
        private TravelCompanionRepository $travelCompanionRepository,
    ) {}

    /**
     * Create a booking for a customer.
     */
    public function createBookingForCustomer($customer, BusTrip $busTrip, int $numOfSeats, array $companions): BusTripBooking
    {
        $totalPrice = $busTrip->ticket_price * $numOfSeats;

        $this->validateCompanionsCount($numOfSeats, $companions);
        $reservedSeats = $this->reserveSeats($busTrip->id, $numOfSeats);
        $seatNumbers = $this->formatReservedSeatNumbers($reservedSeats);

        $busTripBooking = $this->createBooking($customer, $busTrip, $numOfSeats, $totalPrice, $seatNumbers);

        if ($numOfSeats > 1) {
            $this->createCompanions($companions, $busTripBooking->id);
        }

        GenerateQrCodeJob::dispatch($busTripBooking);

        $this->busTripRepository->updateRemainingSeats($busTrip, $busTrip->remaining_seats - $numOfSeats);

        return $busTripBooking;
    }

    /**
     * Validate the companions count matches the expected value.
     */
    private function validateCompanionsCount(int $numOfSeats, array $companions): void
    {
        if ($numOfSeats <= 1) {
            return;
        }

        $expectedCompanionCount = $numOfSeats - 1;
        if (count($companions) !== $expectedCompanionCount) {
            throw new Exception(
                message: sprintf(
                    self::ERROR_COMPANION_COUNT,
                    count($companions),
                    $numOfSeats
                ),
                code: Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Reserve seats for the trip.
     */
    private function reserveSeats(int $busTripId, int $numOfSeats): Collection
    {
        $reservedSeats = $this->busSeatRepository->reserveFirstAvailableSeatsAndLock($busTripId, $numOfSeats);

        if ($reservedSeats->isEmpty()) {
            $availableSeatsCount = $this->busSeatRepository->getAvailableSeats($busTripId)->count();

            if ($availableSeatsCount === 0) {
                throw new Exception(
                    message: self::ERROR_NO_SEATS,
                    code: Response::HTTP_BAD_REQUEST,
                );
            }

            throw new Exception(
                message: sprintf(
                    self::ERROR_INSUFFICIENT_SEATS,
                    $numOfSeats,
                    $availableSeatsCount
                ),
                code: Response::HTTP_BAD_REQUEST,
            );
        }

        if ($reservedSeats->count() !== $numOfSeats) {
            throw new Exception(
                message: sprintf(
                    self::ERROR_SEAT_MISMATCH,
                    $numOfSeats,
                    $reservedSeats->count()
                ),
                code: Response::HTTP_BAD_REQUEST,
            );
        }

        return $reservedSeats;
    }

    /**
     * Format reserved seat numbers into a comma-separated string.
     */
    private function formatReservedSeatNumbers(Collection $reservedSeats): string
    {
        return $reservedSeats->pluck('seat_number')
            ->sort()
            ->implode(',');
    }

    /**
     * Create a booking record.
     */
    private function createBooking($customer, BusTrip $busTrip, int $numOfSeats, float $totalPrice, string $seatNumbers): BusTripBooking
    {
        return $this->busTripBookingRepository->createBusTripBooking([
            'customer_id' => $customer->id,
            'bus_trip_id' => $busTrip->id,
            'reserved_seat_count' => $numOfSeats,
            'qr_code_path' => '',
            'is_departure_confirmed' => false,
            'is_return_confirmed' => false,
            'total_price' => $totalPrice,
            'booking_status' => BookingStatusEnum::UNPAID->value,
            'reserved_seat_numbers' => $seatNumbers,
        ]);
    }

    /**
     * Convert companions from array of strings to array of companion array.
     */
    private function createCompanions(array $companions, int $booking_id): void
    {
        $companionsData = collect($companions)
            ->map(fn($companion): array => [
                'bus_trip_booking_id' => $booking_id,
                'companion_name' => $companion,
            ])
            ->all();

        $this->travelCompanionRepository->insertTravelCompanion($companionsData);
    }
}
