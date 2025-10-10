<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseApiController extends Controller
{
    protected function successResponse(
        string $message = 'messages.success.generic.success',
        int $statusCode = Response::HTTP_OK,
        mixed $data = null,
    ): JsonResponse {
        return ApiResponse::success(
            message: $message,
            statusCode: $statusCode,
            data: $data,
        );
    }

    protected function errorResponse(
        string $message = 'messages.errors.generic.error',
        int $statusCode = Response::HTTP_BAD_REQUEST,
        mixed $data = null,
    ): JsonResponse {
        return ApiResponse::error(
            message: $message,
            statusCode: $statusCode,
            data: $data,
        );
    }
}
