<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth\Dashboard\TravelCompany;

use Illuminate\Http\JsonResponse;
use App\Services\Auth\TravelCompany\TravelCompanyAuthService;
use App\Http\Requests\Api\V1\Auth\Dashboard\TravelCompany\TravelCompanyLoginRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\BaseApiController;
use Exception;

final class TravelCompanyAuthController extends BaseApiController
{
    public function __construct(
        private readonly TravelCompanyAuthService $travelCompanyAuthService,
    ) {}

    public function login(TravelCompanyLoginRequest $travelCompanyLoginRequest): JsonResponse
    {
        try {
            $data = $this->travelCompanyAuthService->authenticateTravelCompanyUser($travelCompanyLoginRequest->validated());

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
