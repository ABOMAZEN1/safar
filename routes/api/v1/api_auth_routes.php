<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\Customer\CustomerAuthController;
use App\Http\Controllers\Api\V1\Auth\BusDriver\BusDriverAuthController;
use App\Http\Controllers\Api\V1\Auth\Dashboard\TravelCompany\TravelCompanyAuthController;

Route::prefix('auth')
    ->as('auth.')
    ->group(static function (): void {
        Route::prefix('bus-drivers')
            ->as('bus-drivers.')
            ->group(static function (): void {
                Route::post('login', [BusDriverAuthController::class, 'login'])->name('login');
            });

        Route::prefix('customers')
            ->as('customers.')
            ->group(static function (): void {
                Route::post('login', [CustomerAuthController::class, 'login'])->name('login');
                Route::post('register', [CustomerAuthController::class, 'register'])->name('register');

                Route::post('verification', [CustomerAuthController::class, 'verify'])->name('verification.verify');
                Route::post('verification/resend', [CustomerAuthController::class, 'resendVerification'])->name('verification.resend');

                Route::post('password/forgot', [CustomerAuthController::class, 'forgotPassword'])->name('password.forgot');
                Route::post('password/verify', [CustomerAuthController::class, 'resetPasswordVerify'])->name('password.verify');
            });

        Route::prefix('travel-companies')
            ->as('travel-companies.')
            ->group(static function (): void {
                Route::post('login', [TravelCompanyAuthController::class, 'login'])->name('login');
            });
    });
