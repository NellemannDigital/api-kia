<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\SynchronizationController;
use App\Http\Controllers\ComplianceTextTemplateController;
use App\Http\Controllers\ComplianceTextController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Bus;

Route::get('/', function () {
    return Inertia::render('auth/login');
})->middleware('guest')->name('home');

Route::middleware(['auth'])->group(function () {

    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::resource('compliance-text', ComplianceTextController::class);
    Route::resource('compliance-text-templates', ComplianceTextTemplateController::class);

    Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
    Route::get('/cars/{id}/prices/', [CarController::class, 'prices'])->name('cars.prices');
    Route::get('/cars/{id}/prices-view/', [CarController::class, 'pricesView'])->name('cars.price.prices');

    Route::get('/cars/{id}/accessories/', [CarController::class, 'accessories'])->name('cars.accessories');
    Route::get('/cars/{id}/accessories-view/', [CarController::class, 'accessoriesView'])->name('cars.accessories-view');

    Route::get('/cars/{id}/specifications/', [CarController::class, 'specifications'])->name('cars.specifications');
    Route::get('/cars/{id}/specifications-view/', [CarController::class, 'specificationsView'])->name('cars.specifications-view');

    Route::post('/cars/{id}/sync/', [CarController::class, 'sync'])->name('cars.sync');

    Route::get('/dealers', [DealerController::class, 'index'])->name('dealers.index');

    Route::prefix('synchronization')->group(function () {
        Route::get('/', [SynchronizationController::class, 'index'])->name('synchronization.index');
        Route::post('pim', [SynchronizationController::class, 'syncPim'])->name('synchronization.pim');
        Route::post('dealers', [SynchronizationController::class, 'syncDealers'])->name('synchronization.dealers');
        Route::post('used-cars', [SynchronizationController::class, 'syncUsedCars'])->name('synchronization.used-cars');
        Route::post('stock-cars', [SynchronizationController::class, 'syncStockCars'])->name('synchronization.stock-cars');
        Route::post('pdfs', [SynchronizationController::class, 'generatePdfs'])->name('synchronization.pdfs');
    });

    Route::prefix('admin')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('users', [UserController::class, 'store'])->name('admin.users.store');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });
});

//Route::get('/specifications', [CarController::class, 'specifications'])->name('specifications');
//Route::get('/specifications-download', [CarController::class, 'specificationsDownload'])->name('specifications-download');
//Route::get('/price-list-download', [CarController::class, 'priceListDownload'])->name('price-list-download');
//Route::get('/price-list-accessories', [CarController::class, 'priceListAccessories'])->name('price-list-accessories');
//Route::get('/price-list-accessories-download', [CarController::class, 'priceListAccessoriesDownload'])->name('price-list-accessories-download');

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
