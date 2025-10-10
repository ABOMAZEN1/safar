<?php

declare(strict_types=1);

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Http\Middleware\ValidatePostSize;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;

/**
 * bootstrap/middleware.php.
 *
 * Registers all middleware aliases and groups.
 */

/** @var Illuminate\Foundation\Configuration\Middleware $middleware */
$middleware->use([
    TrustProxies::class,
    HandleCors::class,
    PreventRequestsDuringMaintenance::class,
    ValidatePostSize::class,
    TrimStrings::class,
    ConvertEmptyStringsToNull::class,
    // SecurityHeaders::class,
]);

// Web Middleware Group
$middleware->group('web', [
    EncryptCookies::class,
    AddQueuedCookiesToResponse::class,
    StartSession::class,
    ShareErrorsFromSession::class,
    ValidateCsrfToken::class,
    SubstituteBindings::class,
]);

// API Middleware Group
$middleware->group('api', [
    EnsureFrontendRequestsAreStateful::class,
    ThrottleRequests::class . ':api',
    SubstituteBindings::class,
]);

// Middleware Aliases
$middleware->alias([
    'auth' => Authenticate::class,
    'auth.basic' => AuthenticateWithBasicAuth::class,
    'auth.session' => AuthenticateSession::class,
    'cache.headers' => SetCacheHeaders::class,
    'can' => Authorize::class,
    'guest' => RedirectIfAuthenticated::class,
    'password.confirm' => RequirePassword::class,
    'signed' => ValidateSignature::class,
    'throttle' => ThrottleRequests::class,
    'bindings' => SubstituteBindings::class,
]);

// Middleware Priority
$middleware->priority([
    HandlePrecognitiveRequests::class,
    EncryptCookies::class,
    AddQueuedCookiesToResponse::class,
    StartSession::class,
    ShareErrorsFromSession::class,
    ValidateCsrfToken::class,
    ThrottleRequests::class,
    SubstituteBindings::class,
    Authorize::class,
]);

$middleware->validateCsrfTokens(except: [
    'api/*',
    'webhook/*',
    'livewire/*',
]);

// Sanctum middleware aliases for API authentication
$middleware->alias([
    'abilities' => CheckAbilities::class,
    'ability' => CheckForAnyAbility::class,
]);
