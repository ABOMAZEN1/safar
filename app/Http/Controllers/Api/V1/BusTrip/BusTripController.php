<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\BusTrip;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Services\BusTrip\BusTripService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\BusTrip\BusTripFilterRequest;
use App\Http\Requests\Api\V1\BusTrip\UpdateBusTripRequest;
use App\Http\Requests\Api\V1\BusTrip\CreateBusTripRequest;
use App\Http\Resources\Api\V1\BusTrip\BusTripResource;

final class BusTripController extends BaseApiController
{
    public function __construct(
        private readonly BusTripService $busTripService,
    ) {}

    public function index(BusTripFilterRequest $busTripFilterRequest): JsonResponse
    {
        try {
            $trips = $this->busTripService->getTrips($busTripFilterRequest->toDto());

            return $this->successResponse(
                message: 'messages.success.fetched',
                data: BusTripResource::collection($trips),
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function seats(int $id): JsonResponse
    {
        try {
            $seats = $this->busTripService->tripSeats($id);

            return $this->successResponse(
                message: 'messages.success.fetched',
                data: ['seats' => $seats],
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function store(CreateBusTripRequest $createBusTripRequest): JsonResponse
    {
        try {
            $this->busTripService->createBusTrip($createBusTripRequest->toDto());

            return $this->successResponse(
                message: 'messages.success.created',
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function update(int $id, UpdateBusTripRequest $updateBusTripRequest): JsonResponse
    {
        try {
            $trip = $this->busTripService->edit($id, $updateBusTripRequest->toDto());

            $trip->load([
                'travelCompany',
                'bus.busType',
                'fromCity',
                'toCity',
                'busDriver.user',
            ]);

            return $this->successResponse(
                message: 'messages.success.updated',
                data: new BusTripResource($trip),
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: Response::HTTP_BAD_REQUEST,
            );
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $trip = $this->busTripService->getTripDetails($id)->load([
                'travelCompany',
                'bus.busType',
                'fromCity',
                'toCity',
                'busDriver',
            ]);

            return $this->successResponse(
                message: 'messages.success.fetched',
                data: (new BusTripResource($trip))->asCompanyView(),
            );
        } catch (Exception) {
            return $this->errorResponse(
                message: 'messages.errors.generic.error',
                statusCode: Response::HTTP_BAD_REQUEST,
            );
        }
    }
}
