<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')->as('dashboard.')->group(static function (): void {
    require_once __DIR__ . '/api_travel_companies_routes.php';
});
