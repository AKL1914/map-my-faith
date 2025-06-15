<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\GamePinController;
use App\Http\Controllers\PinController;
use Illuminate\Support\Facades\Route;

// web push
Route::middleware('web')->post('/save-subscription', function (Illuminate\Http\Request $request) {
    $user = auth()->user();

    if ($user && $request->has('endpoint')) {
        $user->updatePushSubscription(
            $request->input('endpoint'),
            $request->input('keys.p256dh'),
            $request->input('keys.auth')
        );

        return response()->json(['success' => true]);
    }

    return response()->json(['error' => 'Unauthorized'], 401);
});

Route::prefix('v1')->group(function () {
    // Auth for SPA using Google (Socialite)
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/pin', [PinController::class, 'store']);
        Route::get('/pins', [PinController::class, 'index']);
        Route::get('/pins/user/{userId}', [PinController::class, 'indexByUser']);
        Route::get('/pins/bounds', [PinController::class, 'indexByBounds']);
        Route::get('/game-pins', [GamePinController::class, 'index']);
        Route::get('/user/{user}/pins', [PinController::class, 'pinsByUser']);
    });
});
