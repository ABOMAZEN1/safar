<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\TravelCompany\Payment;

use Symfony\Component\HttpFoundation\Response;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\Book\TripIdRequest;
use App\Http\Resources\Api\V1\BusTripBooking\BusTripBookingResource;
use App\Services\Book\BookService;
use Exception;
use Illuminate\Http\JsonResponse;

final class CompanyPaymentController extends BaseApiController
{
    public function __construct(
        private readonly BookService $bookService,
    ) {}

    public function index(TripIdRequest $tripIdRequest): JsonResponse
    {
        try {
            $payments = $this->bookService->getTripPayments($tripIdRequest->validated('bus_trip_id'))
                ->load(['customer.user']);

            return $this->successResponse(
                message: 'messages.success.fetched',
                data: [
                    'total_paid_amount' => $this->bookService->calculateTotalPaidStatusPrice($payments),
                    'total_canceled_amount' => $this->bookService->calculateTotalCanceledOrRefundedStatusPrice($payments),
                    'payments' => BusTripBookingResource::collection($payments)
                        ->map(fn($resource) => $resource->withPaymentDetails()),
                ],
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
