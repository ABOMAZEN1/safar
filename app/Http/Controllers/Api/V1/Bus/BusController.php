<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Bus;

use Exception;
use DomainException;
use App\Services\Bus\BusService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


use App\Http\Resources\Api\V1\Bus\BusResource;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\Bus\CreateBusRequest;
use App\Http\Requests\Api\V1\Bus\UpdateBusRequest;

final class BusController extends BaseApiController
{
    public function __construct(
        private readonly BusService $busService,
    ) {}

    /**
     * Get all buses for authenticated company.
     */
    public function index(): JsonResponse
    {
        try {
            $buses = $this->busService->getCompanyBuses();

            return $this->successResponse(
                message: 'messages.success.fetched',
                data: BusResource::collection($buses),
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Create a new bus.
     */
    public function store(CreateBusRequest $createBusRequest): JsonResponse
    {
        try {
            $bus = $this->busService->createBus($createBusRequest->validated());

            return $this->successResponse(
                message: 'messages.success.created',
                data: new BusResource($bus),
                statusCode: Response::HTTP_CREATED,
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Update the specified bus.
     */
    public function update(UpdateBusRequest $updateBusRequest, int $id): JsonResponse
    {
        try {
            $bus = $this->busService->update($id, $updateBusRequest->validated());

            return $this->successResponse(
                message: 'messages.success.updated',
                data: new BusResource($bus),
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }

    /**
     * Delete the specified bus.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->busService->deleteBus($id);

            return $this->successResponse(
                message: 'messages.success.deleted',
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                message: $exception->getMessage(),
                statusCode: $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
