<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\BusTripBooking;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Services\Book\BookService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\Customer\BookRequest;
use App\Http\Requests\Api\V1\Customer\ListBusTripBookingsRequest;
use App\Http\Resources\Api\V1\Customer\Book\BookQrResource;
use App\Http\Resources\Api\V1\BusTripBooking\BusTripBookingResource;
use Illuminate\Http\Request;

final class BusTripBookingController extends BaseApiController
{
    public function __construct(
        private readonly BookService $bookService,
    ) {}

    public function store(BookRequest $bookRequest): JsonResponse
    {
        try {
            $booking = $this->bookService->book($bookRequest->toDto());

            return $this->successResponse(
                message: __('messages.success.booking.created'),
                data: new BusTripBookingResource($booking),
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function cancel(int $bookingId): JsonResponse
    {
        try {
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

    public function index(ListBusTripBookingsRequest $listBusTripBookingsRequest): JsonResponse
    {
        try {
            $bookings = $this->bookService->getMyBookings($listBusTripBookingsRequest->status)
                ->load([
                    'busTrip',
                    'busTrip.travelCompany',
                    'busTrip.fromCity',
                    'busTrip.toCity',
                ]);

            return $this->successResponse(
                message: __('messages.success.fetched'),
                data: BusTripBookingResource::collection($bookings)
                    ->map(fn($resource) => $resource->withIndexDetails()),
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function showQrCode(int $bookingId): JsonResponse
    {
        try {
            $booking = $this->bookService->getBookingQrDetails($bookingId);
            return $this->successResponse(
                message: __('messages.success.fetched'),
                data: BookQrResource::make($booking),
            );
        } catch (Exception $exception) {
            $statusCode = $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR;

            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $statusCode,
            );
        }
    }

    public function show(int $bookingId): JsonResponse
    {
        try {
            $booking = $this->bookService->getDetails($bookingId)
                ->load([
                    'busTrip',
                    'busTrip.travelCompany',
                    'busTrip.fromCity',
                    'busTrip.toCity',
                    'busTrip.bus',
                    'busTrip.busDriver',
                ]);

            return $this->successResponse(
                message: __('messages.success.fetched'),
                data: (new BusTripBookingResource($booking))->detailed(),
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Verify QR code data for a booking
     */
    public function verifyQrCode(Request $request): JsonResponse
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

            // جلب تفاصيل الحجز مع الرحلة
            $booking = $this->bookService->getDetails($data['booking_id'])
                ->load([
                    'busTrip.travelCompany',
                    'busTrip.fromCity',
                    'busTrip.toCity',
                    'customer.user'
                ]);

            // التحقق من صحة الرحلة
            if ($booking->bus_trip_id != $data['trip_id']) {
                return $this->errorResponse(
                    message: 'QR code does not match the trip',
                    statusCode: Response::HTTP_BAD_REQUEST
                );
            }

            // التحقق من حالة الحجز
            if ($booking->booking_status === 'canceled') {
                return $this->errorResponse(
                    message: 'This booking has been canceled',
                    statusCode: Response::HTTP_BAD_REQUEST
                );
            }

            // التحقق من تاريخ الرحلة
            if ($booking->busTrip->departure_datetime < now()) {
                return $this->errorResponse(
                    message: 'This trip has already departed',
                    statusCode: Response::HTTP_BAD_REQUEST
                );
            }

            return $this->successResponse(
                message: 'QR code verified successfully',
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
                        'company_name' => $booking->busTrip->travelCompany->company_name,
                    ],
                    'verification_time' => now()->toIso8601String(),
                    'qr_data_matches' => true,
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
