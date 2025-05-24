<?php

namespace App\Providers;

use App\Models\Pin;
use App\Observers\PinObserver;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use NotificationChannels\WebPush\WebPushChannel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        $this->app->make(ChannelManager::class)->extend('webpush', function ($app) {
            return $app->make(WebPushChannel::class);
        });
        Pin::observe(PinObserver::class);
        Blade::if('admin', function () {
            return auth()->check() && auth()->user()->is_admin;
        });
    }
}
