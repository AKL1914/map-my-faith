<?php

namespace App\Listeners;

use App\Events\PinCreated;
use Illuminate\Support\Facades\Cache;

/**
 * Class UpdatePinCache
 *
 * Listens for the PinCreated event and updates the cache for pins associated with a campaign.
 */
class UpdatePinCache
{
    /**
     * Handle the event.
     *
     * This method is triggered when a PinCreated event is fired. It invalidates the cache
     * for the pins associated with the campaign of the created pin and updates the cache
     * with the latest pins for the campaign.
     *
     * @param  \App\Events\PinCreated  $event  The event instance containing the created pin.
     * @return void
     */
    public function handle(PinCreated $event)
    {
        $pin = $event->pin; // The pin instance from the event.

        // Define a cache key for the pins of the campaign.
        $cacheKey = 'pins_for_campaign_'.$pin->campaign_id;

        // Invalidate the cache for the campaign's pins if it exists.
        Cache::forget($cacheKey);

        // Fetch all pins associated with the campaign.
        $pins = $pin->campaign->pins;

        // Cache the updated pins data for 10 minutes.
        Cache::put($cacheKey, $pins, 600); // 600 seconds = 10 minutes.
    }
}
