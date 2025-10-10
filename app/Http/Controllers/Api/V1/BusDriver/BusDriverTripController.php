<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\BusDriver;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Services\BusTrip\BusTripService;
use Symfony\Component\HttpFoundation\Response;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\V1\BusTrip\BusTripResource;

final class BusDriverTripController extends BaseApiController
{
    public function __construct(
        private readonly BusTripService $busTripService,
    ) {}

    public function index(): JsonResponse
    {
        try {
            $driverTrips = $this->busTripService->getDriverTrips();

            return $this->successResponse(
                message: 'messages.success.fetched',
                data: BusTripResource::collection($driverTrips)
                    ->map(fn($resource) => $resource->asDriverView()),
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
