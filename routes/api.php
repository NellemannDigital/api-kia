<?php

use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\ComplianceTextController;
use App\Http\Controllers\Api\DealerController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\StockCarController;
use App\Http\Controllers\Api\TestDriveController;
use App\Http\Controllers\Api\UsedCarController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', fn (Request $request) => $request->user());

    Route::prefix('test-drive')
        ->controller(TestDriveController::class)
        ->group(function () {
            Route::get('cars', 'cars');
            Route::get('cars/{id}', 'car');

            Route::get('dealers', 'dealers');
            Route::get('dealers/{dealer}', 'dealer');
            Route::get('dealers/{dealer}/availability', 'availability');

            Route::get('postal-codes', 'postalCodes');
            Route::get('dealers/{dealer}/calendar-availability', 'calendarAvailability');

            Route::post('book', 'book');
        });

    Route::get('/cars', [CarController::class, 'index']);
    Route::get('/cars/{car}', [CarController::class, 'show']);

    Route::get('/dealers', [DealerController::class, 'index']);
    Route::get('/dealers/{dealer}', [DealerController::class, 'show']);

    Route::get('/used-cars', [UsedCarController::class, 'index']);

    Route::get('/stock-cars', [StockCarController::class, 'index']);
    Route::get('/stock-cars-price-range', [StockCarController::class, 'priceRange']);

    Route::get('/compliance-text', [ComplianceTextController::class, 'show'])->name('compliance.text');

    Route::get('/profiles/{profileId}', ProfileController::class);
});
