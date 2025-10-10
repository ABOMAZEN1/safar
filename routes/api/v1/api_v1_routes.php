<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// Apply CORS middleware to all API routes
Route::middleware('api')->group(function (): void {
    require_once __DIR__ . '/api_auth_routes.php';

    require_once __DIR__ . '/api_customers_routes.php';

    require_once __DIR__ . '/api_bus_drivers_routes.php';

    require_once __DIR__ . '/api_public_routes.php';

    require_once __DIR__ . '/dashboard/api_dashboard_routes.php';
});
