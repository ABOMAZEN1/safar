<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Lookup;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\V1\Lookup\LookupResource;
use App\Services\Lookup\LookupService;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class LookupController extends BaseApiController
{
    public function __construct(
        private readonly LookupService $lookupService,
    ) {}

    public function index(): JsonResponse
    {
        try {
            $lookupData = $this->lookupService->getLookupData();

            return $this->successResponse(
                message: __('messages.success.fetched'),
                data: (new LookupResource($lookupData))->resolve(),
            );
        } catch (Exception) {
            return $this->errorResponse(
                message: __('messages.errors.generic.operation_failed'),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
