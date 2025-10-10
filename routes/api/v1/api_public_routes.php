<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Bus\BusController;
use App\Http\Controllers\Api\V1\BusType\BusTypeController;
use App\Http\Controllers\Api\V1\City\CitiesController;
use App\Http\Controllers\Api\V1\BusTripBooking\BusTripBookingController;
use App\Http\Controllers\Api\V1\PrivacyPolicy\PrivacyPolicyController;
use App\Http\Controllers\Api\V1\TermsConditions\TermsConditionsController;
use App\Http\Controllers\Api\V1\TravelCompany\CompanyController;
use App\Http\Controllers\Api\V1\BusTrip\BusTripController;
use App\Http\Controllers\Api\V1\Lookup\LookupController;
use Illuminate\Support\Facades\Route;

Route::prefix('public')->as('public.')->group(static function (): void {
    // Cities
    Route::get('cities', [CitiesController::class, 'index'])->name('cities.index');

    // Travel Companies
    Route::get('travel-companies', [CompanyController::class, 'index'])->name('travel-companies.index');

    // Bus Trips search and seats
    Route::prefix('bus-trips')->as('bus-trips.')->group(static function (): void {
        Route::post('', [BusTripController::class, 'index'])->name('index');
        Route::get('seats/{id}', [BusTripController::class, 'seats'])->name('seats');
        Route::get('qr/{id}', [BusTripBookingController::class, 'showQrCode'])->name('qr.show');

        // New endpoint for verifying QR code data
        Route::post('verify-qr', [BusTripBookingController::class, 'verifyQrCode'])->name('qr.verify');
    });

    // Bus Types
    Route::get('bus-types', [BusTypeController::class, 'index'])->name('bus-types.index');

    // Terms & Conditions
    Route::get('terms-conditions', [TermsConditionsController::class, 'index'])->name('terms-conditions.index');

    // Privacy Policy
    Route::get('privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy.index');

    // Lookup Data
    Route::get('lookup', [LookupController::class, 'index'])->name('lookup.index');

    // Buses (protected)
    Route::middleware(['auth:sanctum'])->group(static function (): void {
        Route::post('buses', [BusController::class, 'store'])->name('buses.store');
    });
});
