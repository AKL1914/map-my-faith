<?php

namespace App\Providers;

use App\Models\Campaign;
use App\Models\Pin;
use App\Observers\CampaignObserver;
use App\Observers\PinObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\PinCreated::class => [\App\Listeners\UpdatePinCache::class],
        \App\Events\PinUpdated::class => [\App\Listeners\UpdatePinCache::class],
        \App\Events\PinDeleted::class => [\App\Listeners\UpdatePinCache::class],
    ];
    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Pin::observe(PinObserver::class);
        Campaign::observe(CampaignObserver::class);
    }
}
