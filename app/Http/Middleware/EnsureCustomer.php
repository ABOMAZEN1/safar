<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use App\Enum\UserTypeEnum;
use App\Helpers\ApiResponse;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class EnsureCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * @var null|User $user
         */
        $user = Auth::user();

        if (! $user) {
            return ApiResponse::error(
                message: 'Unauthenticated',
                statusCode: Response::HTTP_UNAUTHORIZED,
            );
        }

        $isCustomer = $user->type === UserTypeEnum::CUSTOMER->value;
        $hasCustomerRole = $user->roles->contains(static fn($role): bool => $role['role_name'] === UserTypeEnum::CUSTOMER->value);

        if (! $isCustomer && ! $hasCustomerRole) {
            return ApiResponse::error(
                message: 'You are not authorized',
                statusCode: Response::HTTP_FORBIDDEN,
            );
        }

        return $next($request);
    }
}
