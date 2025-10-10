<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth\BusDriver;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\Auth\BusDriver\BusDriverLoginRequest;
use App\Services\Auth\BusDriver\BusDriverAuthService;
use Illuminate\Http\JsonResponse;
use Exception;
use Symfony\Component\HttpFoundation\Response;

final class BusDriverAuthController extends BaseApiController
{
    public function __construct(
        private readonly BusDriverAuthService $busDriverAuthService,
    ) {}

    public function login(BusDriverLoginRequest $busDriverLoginRequest): JsonResponse
    {
        try {
            $data = $this->busDriverAuthService->authenticateBusDriver($busDriverLoginRequest->validated());

            return $this->successResponse(
                message: __('messages.success.login'),
                statusCode: Response::HTTP_OK,
                data: $data,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: __('messages.errors.generic.operation_failed') . ' ' . $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
