<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\BusTripBooking\BusTripBookingController;
use App\Http\Controllers\Api\V1\Customer\CustomerController;
use App\Http\Controllers\Api\V1\Customer\CustomerAccountController;
use App\Http\Middleware\RequireCompleteCustomerProfile;
use App\Http\Middleware\EnsureCustomer;
use Illuminate\Support\Facades\Route;

Route::prefix('customers')->as('customers.')
    ->middleware(['auth:sanctum', EnsureCustomer::class])
    ->group(static function (): void {

        Route::prefix('account')->as('account.')->group(static function (): void {
            Route::post('/', [CustomerController::class, 'store'])->name('store');
            Route::patch('/', [CustomerController::class, 'update'])->name('update');

            Route::post('profile-image', [CustomerAccountController::class, 'updateProfileImage'])->name('update-profile-image');
            Route::post('reset-password', [CustomerAccountController::class, 'resetPassword'])->name('reset-password');

            Route::middleware([RequireCompleteCustomerProfile::class])->group(static function (): void {
                Route::patch('password', [CustomerAccountController::class, 'updatePassword'])->name('update-password');
                Route::get('/', [CustomerController::class, 'getProfile'])->name('profile');
            });
        });

        Route::prefix('bus-trip-bookings')->as('busTripBookings.')->group(static function (): void {
            Route::get('/', [BusTripBookingController::class, 'index'])->name('index');
            Route::get('{id}', [BusTripBookingController::class, 'show'])->name('show');
            Route::post('/', [BusTripBookingController::class, 'store'])->name('store');
            Route::patch('cancel/{id}', [BusTripBookingController::class, 'cancel'])->name('cancel');
        });
    });
