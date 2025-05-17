<?php

use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\GamePinController;
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


        Route::get('/game-pins', [GamePinController::class, 'index']);
        Route::post('/game-pins/{gamePin}/participate', [GamePinController::class, 'participate']);
        Route::post('/pin', [PinController::class, 'store']);
        Route::get('/pins', [PinController::class, 'index']);
        Route::delete('/pin/{id}', [PinController::class, 'destroy']);

        Route::get('/pins/user/{userId}', [PinController::class, 'indexByUser']);
        Route::get('/pins/bounds', [PinController::class, 'indexByBounds']);

        Route::middleware([IsAdmin::class])->group(function () {
            //Campaign API routes
            Route::get('/pins/campaign/{campaignId}', [PinController::class, 'indexByCampaign']);
            Route::apiResource('campaigns', CampaignController::class);

            //User API routes
            Route::get('/users', [UserController::class, 'index']);
            Route::put('/users/{user}/admin', [UserController::class, 'updateAdminStatus']);
            Route::put('/users/{user}/admin', [UserController::class, 'updateAdminStatus']);
            Route::put('/users/{user}/activation', [UserController::class, 'updateActivationStatus']);
            Route::post('/users/activate-all', [UserController::class, 'activateAll']);
            Route::post('/users/deactivate-all', [UserController::class, 'deactivateAll']);
            Route::put('/users/{user}/area-group', [UserController::class, 'updateAreaGroup']);

            //Event API routes
            Route::prefix('events')->group(function () {
                Route::get('/', [EventController::class, 'index']);
                Route::post('/', [EventController::class, 'store']);
                Route::put('/{id}', [EventController::class, 'update']);
                Route::delete('/{id}', [EventController::class, 'destroy']);
                Route::get('/{id}/participants', [EventController::class, 'eventParticipants']);
                Route::get('/{event}/pins', [EventController::class, 'eventPins']);
            });





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
        Route::get('/admin/active-users-count', [DashboardController::class, 'getActiveUsersCount']);
        Route::get('/admin/events', [EventController::class, 'manage']);
        Route::get('/admin/event/{event}/participants', [EventController::class, 'participants']);
        Route::get('/admin/event/{event}/pins', [EventController::class, 'show']);
        Route::get('/admin/game-pins', [\App\Http\Controllers\Admin\GamePinController::class, 'index']);



    });

});

////Admin
Route::get('/activate', [HomeController::class, 'activate'])->name('activate');
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login']);

