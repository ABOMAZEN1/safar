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
}
