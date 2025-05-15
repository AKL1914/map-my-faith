<?php

use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\MapsController;
use App\Http\Controllers\PinController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureUserHasAreaAndGroup;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeaderBoardController;
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
    Route::get('/maps', [MapsController::class, 'index'])->name('maps.index')->middleware(EnsureUserHasAreaAndGroup::class);

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

   // will transfer this bit of route when its needed to restful use
    Route::group(['prefix' => 'api'], function () {
        Route::post('/pin', [PinController::class, 'store']);
        Route::get('/pins', [PinController::class, 'index']);
        Route::delete('/pin/{id}', [PinController::class, 'destroy']);

        Route::get('/pins/user/{userId}', [PinController::class, 'indexByUser']);
        Route::get('/pins/bounds', [PinController::class, 'indexByBounds']);

        Route::middleware([IsAdmin::class])->group(function () {
            Route::get('/pins/campaign/{campaignId}', [PinController::class, 'indexByCampaign']);

            Route::apiResource('campaigns', CampaignController::class);
            Route::get('/users', [UserController::class, 'index']);
            Route::put('/users/{user}/admin', [UserController::class, 'updateAdminStatus']);
            Route::put('/users/{user}/admin', [UserController::class, 'updateAdminStatus']);
            Route::put('/users/{user}/activation', [UserController::class, 'updateActivationStatus']);
            Route::post('/users/activate-all', [UserController::class, 'activateAll']);
            Route::post('/users/deactivate-all', [UserController::class, 'deactivateAll']);
            Route::put('/users/{user}/area-group', [UserController::class, 'updateAreaGroup']);

        });

    });

    //Admin routes
    Route::middleware([IsAdmin::class])->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index']);
        Route::get('/admin/leaderboard', [LeaderBoardController::class, 'index']);
        Route::get('admin/campaigns', [CampaignController::class, 'manage'])->name('admin.campaigns.index');
        Route::get('admin/users', [UserController::class, 'manage'])->name('admin.users.index');
        Route::get('admin/pins', [PinController::class, 'manage'])->name('admin.pins.index');
        Route::get('admin/pin/{pin}', [PinController::class, 'show'])->name('show');

    });

});

////Admin
Route::get('/activate', [HomeController::class, 'activate'])->name('activate');
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login']);

