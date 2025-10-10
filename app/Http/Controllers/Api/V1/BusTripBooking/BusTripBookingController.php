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

            if (!isset($data['id'], $data['signature'])) {
                return $this->errorResponse(
                    message: 'Invalid QR code format',
                    statusCode: Response::HTTP_BAD_REQUEST
                );
            }

            $payload = array_diff_key($data, ['signature' => '']);
            $expectedSignature = hash_hmac('sha256', json_encode($payload, JSON_THROW_ON_ERROR), (string) config('app.key'));

            if (!hash_equals($expectedSignature, $data['signature'])) {
                return $this->errorResponse(
                    message: 'QR code signature verification failed',
                    statusCode: Response::HTTP_UNAUTHORIZED
                );
            }

            $booking = $this->bookService->getDetails($data['id']);

            return $this->successResponse(
                message: 'QR code verified successfully',
                data: [
                    'booking' => $booking,
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
