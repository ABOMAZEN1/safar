<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use App\Helpers\ApiResponse;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class RequireCompleteCustomerProfile
{
    public function handle(Request $request, Closure $next)
    {
        /**
         * @var null|User $user
         */
        $user = Auth::user();

        if (! $user->customer || ! $user->customer->isProfileComplete()) {
            return ApiResponse::error(
                message: 'Please complete your profile information first',
                statusCode: Response::HTTP_FORBIDDEN,
            );
        }

        return $next($request);
    }
}
