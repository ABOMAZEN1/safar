<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\BusType;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\BusType\BusTypeService;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class BusTypeController extends BaseApiController
{
    public function __construct(
        private readonly BusTypeService $busTypeService,
    ) {}

    public function index(): JsonResponse
    {
        try {
            $busTypes = $this->busTypeService->getAllBusTypes();

            return $this->successResponse(
                message: __('messages.success.fetched'),
                data: $busTypes,
            );
        } catch (Exception) {
            return $this->errorResponse(
                message: __('messages.errors.generic.operation_failed'),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
