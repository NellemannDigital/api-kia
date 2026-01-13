<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\Settings\ApiTokenController;

Route::post('/struct/webhook', [WebhookController::class, 'handle']);

Route::get('/cars', [CarController::class, 'index'])->name('cars.index');

Route::view('/test', 'test');


Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->prefix('settings')->group(function () {
    Route::get('/api-tokens', [ApiTokenController::class, 'index']);
    Route::post('/api-tokens', [ApiTokenController::class, 'store']);
    Route::delete('/api-tokens/{id}', [ApiTokenController::class, 'destroy']);
});


require __DIR__.'/settings.php';
