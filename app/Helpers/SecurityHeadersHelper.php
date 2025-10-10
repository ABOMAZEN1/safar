<?php

declare(strict_types=1);

namespace App\Helpers;

final class SecurityHeadersHelper
{
    /**
     * Get the Content Security Policy header value.
     */
    public static function getCSPHeader(): string
    {
        $policies = config('security.csp');
        $header = '';

        foreach ($policies as $directive => $sources) {
            $header .= $directive . ' ' . implode(' ', $sources) . '; ';
        }

        return rtrim($header);
    }

    /**
     * Get CORS headers based on configuration.
     *
     * @return array<string, string>
     */
    public static function getCORSHeaders(): array
    {
        return [
            'Access-Control-Allow-Origin' => config('security.cors.allowed_origins'),
            'Access-Control-Allow-Methods' => config('security.cors.allowed_methods'),
            'Access-Control-Allow-Headers' => config('security.cors.allowed_headers'),
            'Access-Control-Max-Age' => (string) config('security.cors.max_age'),
            'Access-Control-Allow-Credentials' => config('security.cors.supports_credentials') ? 'true' : 'false',
        ];
    }
}
