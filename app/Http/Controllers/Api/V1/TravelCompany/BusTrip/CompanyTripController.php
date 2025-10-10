<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\TravelCompany\BusTrip;

use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\BaseApiController;
use App\Services\BusTrip\BusTripService;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Api\V1\BusTrip\BusTripResource;

final class CompanyTripController extends BaseApiController
{
    public function __construct(
        private readonly BusTripService $busTripService,
    ) {}

    public function index(): JsonResponse
    {
        try {
            $companyTrips = $this->busTripService->getCompanyTrips()->load([
                'travelCompany',
                'bus.busType',
                'fromCity',
                'toCity',
                'busDriver',
            ]);

            return $this->successResponse(
                message: 'messages.success.fetched',
                data: [
                    'bus_trips' => BusTripResource::collection($companyTrips)
                        ->map(fn($resource) => $resource->asCompanyView()),
                ],
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: 'messages.errors.generic.operation_failed',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                data: $exception->getMessage(),
            );
        }
    }

    public function getCreateDetails(): JsonResponse
    {
        try {
            $details = $this->busTripService->getCreateDetails();

            return $this->successResponse(
                message: 'messages.success.fetched',
                data: [
                    'details' => (new BusTripResource($details))->asCreateDetails(),
                ],
            );
        } catch (Exception) {
            return $this->errorResponse(
                message: 'messages.errors.generic.operation_failed',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
