<?php

namespace App\Listeners;

use App\Events\PinCreated;
use Illuminate\Support\Facades\Cache;

class UpdatePinCache
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\PinCreated  $event
     * @return void
     */
    public function handle(PinCreated $event)
    {
        $pin = $event->pin;

        // Define a cache key
        $cacheKey = 'pins_for_campaign_' . $pin->campaign_id;

        // Invalidate the cache if necessary
        Cache::forget($cacheKey);

        // You could update the cache with the latest pins, e.g., fetching all pins for the campaign
        $pins = $pin->campaign->pins;

        // Cache the updated data
        Cache::put($cacheKey, $pins, 600); // Cache for 10 minutes (adjust as necessary)
    }
}
