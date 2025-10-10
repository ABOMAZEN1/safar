<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\BusDriver;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Services\Book\BookService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\BusDriver\ConfirmReturnRequest;
use App\Http\Requests\Api\V1\BusDriver\ConfirmDepartureRequest;

final class DriverBusTripBookingController extends BaseApiController
{
    public function __construct(
        private readonly BookService $bookService,
    ) {}

    public function confirmDeparture(ConfirmDepartureRequest $confirmDepartureRequest): JsonResponse
    {
        try {
            $bookingId = (int) $confirmDepartureRequest->validated('booking_id');

            $this->bookService->confirmDeparture($bookingId);

            return $this->successResponse(
                message: 'messages.success.booking.confirmed',
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function confirmReturn(ConfirmReturnRequest $confirmReturnRequest): JsonResponse
    {
        try {
            $bookingId = (int) $confirmReturnRequest->validated('booking_id');

            $this->bookService->confirmReturn($bookingId);

            return $this->successResponse(
                message: 'messages.success.booking.confirmed',
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
