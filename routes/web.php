use App\Http\Controllers\Admin\TravelCompanyReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'filament.admin'])->group(function (): void {
    Route::get('/admin/reports/travel-companies/{travelCompany}', TravelCompanyReportController::class)
        ->name('admin.reports.travel-companies.show');
});
<?php

 
use Illuminate\Support\Facades\Route;

Route::get('/', static function () {
    return response()->json([
        'success' => true,
        'message' => 'Backend is running',
        'status_code' => 200,
        'timestamp' => now()->toIso8601String(),
    ]);
});

use App\Http\Controllers\PredefinedMessageController;

Route::prefix('admin/predefined-messages')->group(function () {
    Route::get('/', [PredefinedMessageController::class, 'index'])->name('admin.predefined-messages.index');
    Route::post('/', [PredefinedMessageController::class, 'store'])->name('admin.predefined-messages.store');
    Route::put('/{predefinedMessage}', [PredefinedMessageController::class, 'update'])->name('admin.predefined-messages.update');
    Route::delete('/{predefinedMessage}', [PredefinedMessageController::class, 'destroy'])->name('admin.predefined-messages.destroy');
});

