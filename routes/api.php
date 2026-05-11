<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestDriveController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\DealerController;
use App\Http\Controllers\Api\UsedCarController;
use App\Http\Controllers\Api\StockCarController;
use App\Http\Controllers\Api\ComplianceTextController;
use App\Http\Controllers\Api\ActivityController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
 Route::get('test-drive/dealers/{dealer}/availability', [TestDriveController::class, 'availability']);

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('test-drive')->group(function () {
        Route::get('cars', [TestDriveController::class, 'cars']);
        Route::get('dealers', [TestDriveController::class, 'dealers']);

        Route::get('postal-codes', [TestDriveController::class, 'postalCodes']);

       // Route::get('dealers/{dealer}/availability', [TestDriveController::class, 'availability']);
        Route::get('dealers/{dealer}/calendar-availability', [TestDriveController::class, 'calendarAvailability']);

        Route::post('book', [TestDriveController::class, 'book']);
    });

    Route::get('/cars', [CarController::class, 'index']);
    Route::get('/cars/{car}', [CarController::class, 'show']);

    Route::get('/dealers', [DealerController::class, 'index']);

    Route::get('/used-cars', [UsedCarController::class, 'index']);

    Route::get('/stock-cars', [StockCarController::class, 'index']);
    Route::get('/stock-cars-price-range', [StockCarController::class, 'priceRange']);

    Route::get('/compliance-text', [ComplianceTextController::class, 'show'])->name('compliance.text');
});
