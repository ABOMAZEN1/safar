<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Bus\BusController;
use App\Http\Controllers\Api\V1\BusType\BusTypeController;
use App\Http\Controllers\Api\V1\BusDriver\BusDriverController;
use App\Http\Controllers\Api\V1\TravelCompany\BusTripBooking\CompanyBusTripBookingController;
use App\Http\Controllers\Api\V1\TravelCompany\Payment\CompanyPaymentController;
use App\Http\Controllers\Api\V1\TravelCompany\Payment\RefundController;
use App\Http\Controllers\Api\V1\TravelCompany\Statistics\CompanyStatistics;
use App\Http\Controllers\Api\V1\TravelCompany\BusTrip\CompanyTripController;
use App\Http\Controllers\Api\V1\BusTrip\BusTripController;
use App\Http\Middleware\EnsureTravelCompany;
use Illuminate\Support\Facades\Route;

Route::prefix('travel-companies')->as('travel-companies.')
    ->middleware(['auth:sanctum', EnsureTravelCompany::class])
    ->group(static function (): void {
        // Trips
        Route::prefix('trips')->as('trips.')->group(static function (): void {
            Route::get('/', [CompanyTripController::class, 'index'])->name('index');
            Route::post('/', [BusTripController::class, 'store'])->name('store');
            Route::get('create', [CompanyTripController::class, 'getCreateDetails'])->name('create');
            Route::get('{id}', [BusTripController::class, 'show'])->name('show');
            Route::patch('{id}', [BusTripController::class, 'update'])->name('update');
        });

        // Drivers
        Route::prefix('drivers')->as('drivers.')->group(static function (): void {
            Route::get('/', [BusDriverController::class, 'index'])->name('index');
            Route::post('/', [BusDriverController::class, 'store'])->name('store');
            Route::patch('{id}', [BusDriverController::class, 'update'])->name('update');
            Route::delete('{id}', [BusDriverController::class, 'destroy'])->name('destroy');
        });

        Route::get('bus-types', [BusTypeController::class, 'index'])->name('bus-types.index');

        Route::prefix('buses')->as('buses.')->group(static function (): void {
            Route::get('/', [BusController::class, 'index'])->name('index');
            Route::post('/', [BusController::class, 'store'])->name('store');
            Route::patch('{id}', [BusController::class, 'update'])->name('update');
            Route::delete('{id}', [BusController::class, 'destroy'])->name('destroy');
        });

        // Bookings
        Route::prefix('bookings')->as('bookings.')->group(static function (): void {
            Route::get('/', [CompanyBusTripBookingController::class, 'index'])->name('index');
            Route::get('{id}', [CompanyBusTripBookingController::class, 'show'])->name('show');
            Route::post('/', [CompanyBusTripBookingController::class, 'store'])->name('store');
            Route::post('phone', [CompanyBusTripBookingController::class, 'storeByPhone'])->name('store-by-phone');
            Route::patch('{id}/cancel', [CompanyBusTripBookingController::class, 'cancel'])->name('cancel');
        });

        // Payments
        Route::get('payments', [CompanyPaymentController::class, 'index'])->name('payments.index');

        // Refunds
        Route::post('refunds', [RefundController::class, 'processRefund'])->name('refunds.process');

        // Statistics
        Route::get('statistics', [CompanyStatistics::class, 'index'])->name('statistics.index');
    });
