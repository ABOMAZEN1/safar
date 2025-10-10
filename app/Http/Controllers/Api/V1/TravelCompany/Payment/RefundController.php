<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\TravelCompany\Payment;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\Book\RefundBookingRequest;
use App\Services\Book\BookService;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class RefundController extends BaseApiController
{
    public function __construct(
        private readonly BookService $bookService,
    ) {}

    public function processRefund(RefundBookingRequest $refundBookingRequest): JsonResponse
    {
        try {
            $this->bookService->refund($refundBookingRequest->validated('booking_id'));

            return $this->successResponse(
                message: 'messages.success.refund.processed',
            );
        } catch (Exception) {
            return $this->errorResponse(
                message: 'messages.errors.generic.invalid_request',
                statusCode: Response::HTTP_BAD_REQUEST,
            );
        }
    }
}
