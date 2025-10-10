<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\BusDriver\DriverBusTripBookingController;
use App\Http\Controllers\Api\V1\BusDriver\BusDriverController;
use App\Http\Controllers\Api\V1\BusDriver\BusDriverTripController;
use App\Http\Middleware\EnsureBusDriver;
use Illuminate\Support\Facades\Route;

Route::prefix('bus-drivers')->as('busDrivers.')
    ->middleware(['auth:sanctum', EnsureBusDriver::class])
    ->group(static function (): void {
        Route::prefix('bus-trips')->as('busTrips.')->group(static function (): void {
            Route::get('/', [BusDriverTripController::class, 'index'])->name('index');
        });

        Route::prefix('bookings')->as('bookings.')->group(static function (): void {
            Route::patch('confirm-departure', [DriverBusTripBookingController::class, 'confirmDeparture'])->name('confirm-departure');
            Route::patch('confirm-return', [DriverBusTripBookingController::class, 'confirmReturn'])->name('confirm-return');
        });

        Route::prefix('profile')->as('profile.')->group(static function (): void {
            Route::get('/', [BusDriverController::class, 'profile'])->name('show');
            Route::post('reset-password', [BusDriverController::class, 'resetPassword'])->name('reset-password');
        });
    });
