<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\TermsConditions;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\TermsConditionsService\TermsConditionsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class TermsConditionsController extends BaseApiController
{
    public function __construct(private readonly TermsConditionsService $termsConditionsService) {}

    public function index(): JsonResponse
    {
        try {
            $termsConditions = $this->termsConditionsService->getTermsConditions();

            return $this->successResponse(
                message: 'messages.success.fetched',
                data: $termsConditions,
            );
        } catch (Exception) {
            return $this->errorResponse(
                message: 'messages.errors.generic.operation_failed',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
