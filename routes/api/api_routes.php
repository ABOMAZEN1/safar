<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::group(['as' => 'api.'], function (): void {
    Route::prefix('v1')->as('v1.')->group(static function (): void {
        require __DIR__ . '/v1/api_v1_routes.php';
    });

    // Notification routes
    require __DIR__ . '/notifications.php';

    // Simple health check endpoint
    Route::get('health', static function () {
        return response()->json([
            'ok' => true,
            'timestamp' => now()->toIso8601String(),
        ]);
    })->name('health');
});
