<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SecurityHeaders
{
    /**
     * Security headers that will be applied to all responses.
     *
     * @var array<string, string>
     */
    private const SECURITY_HEADERS = [
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1; mode=block',
        'X-Frame-Options' => 'DENY',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
        'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
        'Content-Security-Policy' => "default-src 'self'; img-src 'self' data: https:; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; connect-src 'self' https:;",
        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type, X-Token-Auth, Authorization, Accept-Language',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Apply security headers
        foreach (self::SECURITY_HEADERS as $headerKey => $headerValue) {
            $response->headers->set($headerKey, $headerValue);
        }

        // Handle CORS for API routes
        if ($request->is('api/*')) {
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');

            // Handle preflight requests
            if ($request->getMethod() === 'OPTIONS') {
                $response->headers->set('Access-Control-Max-Age', '86400');

                return $response;
            }
        }

        return $response;
    }
}
