<?php

use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\MapsController;
use App\Http\Controllers\PinController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeanerBoardController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/callback/google', [LoginController::class, 'handleGoogleCallback']);
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

Route::get('/login', function () {
    return redirect('/');
})->name('login');


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/maps', [MapsController::class, 'index'])->name('maps.index');
   // will transfer this bit of route when its needed to restful use
    Route::group(['prefix' => 'api'], function () {
        Route::post('/pin', [PinController::class, 'store']);
        Route::get('/pins', [PinController::class, 'index']);
        Route::get('/pins/user/{userId}', [PinController::class, 'indexByUser']);
        Route::get('/pins/bounds', [PinController::class, 'indexByBounds']);

        Route::middleware([IsAdmin::class])->group(function () {
            Route::get('/pins/campaign/{campaignId}', [PinController::class, 'indexByCampaign']);
            Route::apiResource('campaigns', CampaignController::class);
            Route::get('/users', [UserController::class, 'index']);
            Route::put('/users/{user}/admin', [UserController::class, 'updateAdminStatus']);
        });

    });

    //Admin routes
    Route::middleware([IsAdmin::class])->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index']);
        Route::get('/admin/leaderboard', [LeanerBoardController::class, 'index']);
        Route::get('admin/campaigns', [CampaignController::class, 'manage'])->name('admin.campaigns.index');
        Route::get('admin/users', [UserController::class, 'manage'])->name('admin.users.index');
    });

});

////Admin
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login']);

