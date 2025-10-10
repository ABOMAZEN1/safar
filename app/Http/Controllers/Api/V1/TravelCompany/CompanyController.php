<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\TravelCompany;


use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\V1\TravelCompany\TravelCompanyResource;
use App\Services\TravelCompany\CompanyService;
use App\Http\Requests\Api\V1\TravelCompany\TravelCompanyFilterRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
final class CompanyController extends BaseApiController
{
    public function __construct(
        private readonly CompanyService $companyService,
    ) {}

    /**
     * Get all active travel companies with optional filtering.
     */
    public function index(TravelCompanyFilterRequest $travelCompanyFilterRequest): JsonResponse
    {
        try {
            $companies = $this->companyService->getActiveTravelCompanies($travelCompanyFilterRequest->toDto());

            return $this->successResponse(
                message: __('messages.success.fetched'),
                data: TravelCompanyResource::collection($companies),
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: __('messages.errors.generic.operation_failed') . ' ' . $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $company = $this->companyService->getTravelCompany($id)
                ->load(['user', 'busTrips', 'buses', 'commission']);

            return $this->successResponse(
                message: __('messages.success.fetched'),
                data: new TravelCompanyResource($company),
            );
        } catch (Exception) {
            return $this->errorResponse(
                message: __('messages.errors.generic.operation_failed'),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
