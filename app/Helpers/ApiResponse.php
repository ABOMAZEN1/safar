<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ApiResponse
{
    public static function success(string $message = null, int $statusCode = Response::HTTP_OK, mixed $data = null): JsonResponse
    {
        $message = $message ?? __('messages.success.generic.success');
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'status_code' => $statusCode,
            'timestamp' => now()->toIso8601String(),
        ], $statusCode);
    }

    public static function error(string $message = null, int $statusCode = Response::HTTP_BAD_REQUEST, mixed $data = null): JsonResponse
    {
        $message = $message ?? __('messages.errors.generic.error');
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $data,
            'status_code' => $statusCode,
            'timestamp' => now()->toIso8601String(),
        ], $statusCode);
    }
}
