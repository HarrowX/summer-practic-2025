<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('/promo/apply', [PromoController::class, 'apply']);
});
