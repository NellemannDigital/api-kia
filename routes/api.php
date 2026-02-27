<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\DealerController;
use App\Http\Controllers\Api\UsedCarController;
use App\Http\Controllers\Api\StockCarController;
use App\Http\Controllers\Api\ComplianceTextController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cars', [CarController::class, 'index']);
    Route::get('/cars/{car}', [CarController::class, 'show']);

    Route::get('/dealers', [DealerController::class, 'index']);

    Route::get('/used-cars', [UsedCarController::class, 'index']);

    Route::get('/stock-cars', [StockCarController::class, 'index']);
    Route::get('/stock-cars-price-range', [StockCarController::class, 'priceRange']);
});

Route::get('/compliance-text', [ComplianceTextController::class, 'show']);