<?php

use App\Http\Controllers\LauncherController;
use App\Http\Controllers\PromoController;

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('/promo/apply', [PromoController::class, 'apply']);
});

Route::post('/launcher/generate-code', [LauncherController::class, 'generateCode']);
Route::post('/launcher/pay', [LauncherController::class, 'processPayment']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/verify-code', [LauncherController::class, 'verifyCode']);
});
