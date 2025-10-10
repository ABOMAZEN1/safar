<?php

declare(strict_types=1);

/**
 * bootstrap/exceptions.php.
 *
 * Custom exception rendering for API and web responses.
 */

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Lottery;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Psr\Log\LogLevel;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Configuration\Exceptions;

/** @var Exceptions $exceptions */
$exceptions->dontFlash([
    'password',
    'password_confirmation',
    'password_confirm',
]);

$exceptions->dontReportDuplicates();

$exceptions->level(PDOException::class, LogLevel::CRITICAL);

$exceptions->throttle(function (Throwable $e) {
    return Lottery::odds(1, 1000);
});

$exceptions->render(function (NotFoundHttpException $exception, Request $request) {
    return ApiResponse::error('Record not found', 404, $exception->getMessage());
});

$exceptions->render(function (AuthorizationException $exception, Request $request) {
    return ApiResponse::error('This action is unauthorized', 403, $exception->getMessage());
});

$exceptions->render(function (AccessDeniedHttpException $exception, Request $request) {
    return ApiResponse::error('This action is unauthorized', 403, $exception->getMessage());
});

// $exceptions->render(function (QueryException $exception, Request $request) {
//     return ApiResponse::error('An error occurred while retrieving data. Please try again later.', 500, $exception->getMessage());
// });

$exceptions->render(function (AuthenticationException $exception, Request $request) {
    return ApiResponse::error('You have to login first', 401, $exception->getMessage());
});

// $exceptions->render(function (Throwable $e) {
//     return ApiResponse::error('An error occurred while retrieving data. Please try again later.', 500, $e->getMessage());
// });


// Update the API middleware check
$exceptions->shouldRenderJsonWhen(function (Request $request) {
    // Explicitly exclude admin routes from JSON responses
    if ($request->is('admin*')) {
        return false;
    }

    return $request->is('api/*') ||
        $request->expectsJson() ||
        $request->is('admin/api/*');
});


$exceptions->respond(function (Response $response) {
    if ($response->getStatusCode() === 419) {
        return back()->with([
            'message' => 'The page expired, please try again.',
        ]);
    }

    return $response;
});

$exceptions->render(function (AuthenticationException $exception, Request $request) {
    if ($request->is('api/*') || $request->expectsJson()) {
        return ApiResponse::error(
            'You have to login first',
            401,
            $exception->getMessage(),
        );
    }

    if ($request->is('admin/*')) {
        return redirect()->guest(route('filament.admin.auth.login'));
    }

    return redirect()->guest(route('login'));
});

$exceptions->render(function (NotFoundHttpException $exception, Request $request) {
    $message = $exception->getMessage() ?: 'The page you are looking for could not be found.';

    if ($request->is('api/*') || $request->expectsJson()) {
        return ApiResponse::error($message, 404);
    }

    return response()->view('errors.404', ['message' => $message], 404);
});

$exceptions->render(function (AuthorizationException $exception, Request $request) {
    if ($request->is('api/*') || $request->expectsJson()) {
        return ApiResponse::error(
            'This action is unauthorized',
            403,
            $exception->getMessage(),
        );
    }

    return redirect()
        ->back()
        ->with('error', 'You are not authorized to perform this action.');
});

$exceptions->render(function (Throwable $e, Request $request) {
    if ($request->is('api/*') || $request->expectsJson()) {
        return ApiResponse::error(
            'An error occurred while processing your request' . $e->getMessage(),
            500,
            $e->getMessage(),
        );
    }

    return null;
});
