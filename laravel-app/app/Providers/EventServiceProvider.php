<?php

namespace App\Providers;

use App\Models\Campaign;
use App\Observers\CampaignObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Campaign::observe(CampaignObserver::class);
    }
}
