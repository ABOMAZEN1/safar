<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\PrivacyPolicy;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\PrivacyPolicy\PrivacyPolicyService;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class PrivacyPolicyController extends BaseApiController
{
    public function __construct(
        private readonly PrivacyPolicyService $privacyPolicyService,
    ) {}

    public function index(): JsonResponse
    {
        try {
            $privacyPolicy = $this->privacyPolicyService->getPrivacyPolicy();

            return $this->successResponse(
                message: __('messages.success.fetched'),
                data: $privacyPolicy,
            );
        } catch (Exception) {
            return $this->errorResponse(
                message: __('messages.errors.generic.operation_failed'),
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
