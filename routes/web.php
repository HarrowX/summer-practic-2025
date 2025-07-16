<?php

use App\Http\Controllers\PromoController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'can:manage-promos'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('promos', PromoController::class)
            ->except(['show']);

        Route::get('/promos/list', function () {
            return view('admin.promos.index');
        })->name('promos.list');
    });

Route::middleware('auth')->group(function () {
    Route::get('/promo', function () {
        return view('promo-page');
    })->name('promo');

    Route::prefix('api')->group(function () {
        Route::post('/promo/apply', [PromoController::class, 'apply']);
    });
});

require __DIR__.'/auth.php';
