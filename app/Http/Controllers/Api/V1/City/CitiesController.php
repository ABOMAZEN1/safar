<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\City;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\City\CityFilterRequest;
use App\Http\Resources\Api\V1\City\CityResource;
use App\Services\City\CityService;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class CitiesController extends BaseApiController
{
    public function __construct(
        private readonly CityService $cityService,
    ) {}

    public function index(CityFilterRequest $cityFilterRequest): JsonResponse
    {
        try {
            $filterDTO = $cityFilterRequest->toDTO();
            $cities = $this->cityService->getAllCities($filterDTO);

            return $this->successResponse(
                message: __('messages.success.fetched'),
                data: CityResource::collection($cities),
            );
        } catch (Exception) {
            return $this->errorResponse(
                message: __('messages.errors.generic.operation_failed'),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
