<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\DealerController;
use App\Http\Controllers\Api\UsedCarController;
use App\Http\Controllers\Api\StockCarController;
use App\Http\Controllers\Api\ComplianceTextController;
use App\Http\Controllers\Api\ActivityController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cars', [CarController::class, 'index']);
    Route::get('/cars/{car}', [CarController::class, 'show']);

    Route::get('/dealers', [DealerController::class, 'index']);

    Route::prefix('dealers')->group(function () {
        Route::get('{dealer}/availability', [DealerController::class, 'availability']);
        Route::get('{dealer}/calendar-availability', [DealerController::class, 'calendarAvailability']);
    });

     Route::prefix('activities')->group(function () {
        Route::post('test-drive', [ActivityController::class, 'testDrive']);
    });

    Route::get('/used-cars', [UsedCarController::class, 'index']);

    Route::get('/stock-cars', [StockCarController::class, 'index']);
    Route::get('/stock-cars-price-range', [StockCarController::class, 'priceRange']);
});

Route::get('/compliance-text', [ComplianceTextController::class, 'show'])->name('compliance.text');
