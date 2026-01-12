<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\CarController;

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

require __DIR__.'/settings.php';
