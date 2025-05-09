<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\PinController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    // Auth for SPA using Google (Socialite)
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/pin', [PinController::class, 'store']);
        Route::get('/pins', [PinController::class, 'index']);
        Route::get('/pins/user/{userId}', [PinController::class, 'indexByUser']);
        Route::get('/pins/bounds', [PinController::class, 'indexByBounds']);
    });
});
