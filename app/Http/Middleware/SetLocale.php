<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

use function in_array;

final class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $locale = $request->header('Accept-Language');

        if ($locale && in_array($locale, config('app.available_locales'), true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
