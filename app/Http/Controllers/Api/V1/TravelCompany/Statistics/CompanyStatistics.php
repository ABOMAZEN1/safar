<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\TravelCompany\Statistics;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\TravelCompany\CompanyService;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class CompanyStatistics extends BaseApiController
{
    public function __construct(
        private readonly CompanyService $companyService,
    ) {}

    public function index(): JsonResponse
    {
        try {
            $data = $this->companyService->getCompanyStatistics();

            return $this->successResponse(
                message: __('messages.success.fetched'),
                data: $data,
            );
        } catch (Exception) {
            return $this->errorResponse(
                message: __('messages.errors.generic.operation_failed'),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
