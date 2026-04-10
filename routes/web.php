<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\SynchronizationController;
use App\Http\Controllers\Settings\ApiTokenController;
use App\Http\Controllers\ComplianceTextTemplateController;
use App\Http\Controllers\ComplianceTextController;
use Illuminate\Support\Facades\Bus;

Route::post('/struct/webhook', [WebhookController::class, 'handle']);

Route::view('/test', 'test');

Route::get('/specifications', [CarController::class, 'specifications'])->name('specifications');
Route::get('/specifications-download', [CarController::class, 'specificationsDownload'])->name('specifications-download');
Route::get('/price-list-download', [CarController::class, 'priceListDownload'])->name('price-list-download');
Route::get('/price-list-accessories', [CarController::class, 'priceListAccessories'])->name('price-list-accessories');
Route::get('/price-list-accessories-download', [CarController::class, 'priceListAccessoriesDownload'])->name('price-list-accessories-download');

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::resource('compliance-text-templates', ComplianceTextTemplateController::class);

    Route::resource('settings/api-tokens', ApiTokenController::class);

    Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
    Route::post('/cars/sync/{id}', [CarController::class, 'sync'])->name('cars.sync');
    
    Route::get('/cars/{id}/price-list/', [CarController::class, 'priceList'])->name('cars.price-list');

    Route::get('/dealers', [DealerController::class, 'index'])->name('dealers.index');

    Route::get('/synchronization', [SynchronizationController::class, 'index'])->name('synchronization.index');
    Route::post('/synchronization/pim', [SynchronizationController::class, 'syncPim'])->name('synchronization.pim');
    Route::post('/synchronization/dealers', [SynchronizationController::class, 'syncDealers'])->name('synchronization.dealers');
    Route::post('/synchronization/used-cars', [SynchronizationController::class, 'syncUsedCars'])->name('synchronization.used-cars');
    Route::post('/synchronization/stock-cars', [SynchronizationController::class, 'syncStockCars'])->name('synchronization.stock-cars');

    Route::get('/compliance-text', [ComplianceTextController::class, 'index'])->name('compliance.index');
});

Route::get('/embed/compliance.js', function () {
    return response()->file(public_path('embed/compliance.js'), [
        'Content-Type' => 'application/javascript',
    ]);
});

Route::get('/batches/{id}', function ($id) {
    $batch = Bus::findBatch($id);

    if (!$batch) {
        return response()->json(['error' => 'Batch not found'], 404);
    }

    return response()->json([
        'progress' => $batch->progress(),
        'finished' => $batch->finished(),
        'failed' => $batch->failedJobs,
    ]);
});



require __DIR__.'/settings.php';
