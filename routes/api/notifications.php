<?php

use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Notification API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register notification API routes for your application.
| These routes are protected by authentication middleware.
|
*/
Route::middleware('auth:sanctum')->post('/fcm/token', [App\Http\Controllers\FcmTokenController::class, 'store']);

Route::middleware(['auth:sanctum'])->group(function () {
    // FCM Token management
    Route::post('/users/fcm-token', [NotificationController::class, 'updateFcmToken'])
        ->name('api.notifications.update-fcm-token');
    
    // Notification statistics
    Route::get('/notifications/stats', [NotificationController::class, 'notificationStats'])
        ->name('api.notifications.stats');
    
    // Validate FCM token
    Route::post('/validate-token', [NotificationController::class, 'validateFcmToken'])
        ->name('api.notifications.validate-token');
});
