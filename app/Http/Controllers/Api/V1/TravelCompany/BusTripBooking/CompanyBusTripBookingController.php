<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\TravelCompany\BusTripBooking;

use Symfony\Component\HttpFoundation\Response;


use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\Book\CancelBusTripBookingRequest;
use App\Http\Requests\Api\V1\Book\TripBookingByPhoneRequest;
use App\Http\Requests\Api\V1\Book\TripBookingsRequest;
use App\Http\Requests\Api\V1\Book\TripIdRequest;
use App\Http\Resources\Api\V1\CompanyBooking\TravelCompanyBookingResource;
use App\Services\Book\BookService;
use Exception;
use Illuminate\Http\JsonResponse;

final class CompanyBusTripBookingController extends BaseApiController
{
    public function __construct(
        private readonly BookService $bookService,
    ) {}

    /**
     * Get all bookings for the authenticated company, optionally filtered by bus_trip_id.
     */
    public function index(TripIdRequest $tripIdRequest): JsonResponse
    {
        try {
            $tripId = $tripIdRequest->validated('bus_trip_id');
            $bookings = $this->bookService->getTripBookings($tripId);

            return $this->successResponse(
                message: __('messages.success.fetched'),
                data: TravelCompanyBookingResource::collection($bookings),
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Get booking details by ID.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $booking = $this->bookService->getDetails($id);

            return $this->successResponse(
                message: __('messages.success.fetched'),
                data: TravelCompanyBookingResource::make($booking),
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Create a new booking by a travel company for a customer.
     */
    public function store(TripBookingsRequest $tripBookingsRequest): JsonResponse
    {
        try {
            $dto = $tripBookingsRequest->toDto();
            $busTripBooking = $this->bookService->createCompanyBooking($dto);

            return $this->successResponse(
                message: __('messages.success.booking.created'),
                data: TravelCompanyBookingResource::make($busTripBooking),
                statusCode: Response::HTTP_CREATED,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Create a booking for an existing customer using their phone number.
     */
    public function storeByPhone(TripBookingByPhoneRequest $tripBookingByPhoneRequest): JsonResponse
    {
        try {
            $dto = $tripBookingByPhoneRequest->toDto();
            $busTripBooking = $this->bookService->createBookingByPhone($dto);

            return $this->successResponse(
                message: __('messages.success.booking.created'),
                data: TravelCompanyBookingResource::make($busTripBooking),
                statusCode: Response::HTTP_CREATED,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Cancel a booking.
     */
    public function cancel(CancelBusTripBookingRequest $cancelBusTripBookingRequest): JsonResponse
    {
        try {
            $bookingId = $cancelBusTripBookingRequest->validated('booking_id');

            $this->bookService->cancel($bookingId);

            return $this->successResponse(
                message: __('messages.success.booking.canceled'),
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Verify booking QR code for company staff
     */
    public function verifyBookingQr(Request $request): JsonResponse
    {
        try {
            $data = json_decode((string) $request->input('qr_data'), true, 512, JSON_THROW_ON_ERROR);

            if (!isset($data['booking_id'], $data['signature'])) {
                return $this->errorResponse(
                    message: 'Invalid QR code format',
                    statusCode: Response::HTTP_BAD_REQUEST
                );
            }

            // التحقق من التوقيع الرقمي
            $payload = array_diff_key($data, ['signature' => '']);
            $expectedSignature = hash_hmac('sha256', json_encode($payload, JSON_THROW_ON_ERROR), (string) config('app.key'));

            if (!hash_equals($expectedSignature, $data['signature'])) {
                return $this->errorResponse(
                    message: 'QR code signature verification failed',
                    statusCode: Response::HTTP_UNAUTHORIZED
                );
            }

            // جلب تفاصيل الحجز
            $booking = $this->bookService->getDetails($data['booking_id'])
                ->load(['busTrip.travelCompany', 'busTrip.fromCity', 'busTrip.toCity', 'customer.user']);

            // التحقق من أن الحجز يخص هذه الشركة
            $companyId = auth()->user()->travelCompany->id;
            if ($booking->busTrip->travel_company_id != $companyId) {
                return $this->errorResponse(
                    message: 'This booking does not belong to your company',
                    statusCode: Response::HTTP_FORBIDDEN
                );
            }

            return $this->successResponse(
                message: 'Booking verified successfully',
                data: [
                    'booking' => [
                        'id' => $booking->id,
                        'customer_name' => $booking->customer->user->name,
                        'phone_number' => $booking->customer->user->phone_number,
                        'seats' => $booking->reserved_seat_numbers,
                        'seat_count' => $booking->reserved_seat_count,
                        'total_price' => $booking->total_price,
                        'booking_status' => $booking->booking_status,
                        'is_departure_confirmed' => $booking->is_departure_confirmed,
                    ],
                    'trip' => [
                        'id' => $booking->busTrip->id,
                        'from_city' => $booking->busTrip->fromCity->name,
                        'to_city' => $booking->busTrip->toCity->name,
                        'departure_datetime' => $booking->busTrip->departure_datetime->toIso8601String(),
                    ],
                    'verification_time' => now()->toIso8601String(),
                ]
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
