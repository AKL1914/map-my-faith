<?php

namespace App\Observers;

use App\Models\Pin;
use Illuminate\Support\Facades\Cache;

/**
 * Class PinObserver
 *
 * Observes the lifecycle events of the Pin model and performs actions such as
 * clearing cache when a pin is created, updated, deleted, restored, or force deleted.
 */
class PinObserver
{
    /**
     * Handle the Pin "created" event.
     *
     * Clears the bounds cache and the cache for all pins when a new pin is created.
     *
     * @param  Pin  $pin  The pin instance that was created.
     */
    public function created(Pin $pin): void
    {
        $this->clearAllBoundsCache();
        Cache::forget('all_pins');
    }

    /**
     * Handle the Pin "updated" event.
     *
     * Clears the bounds cache and specific caches related to the pin's campaign and user
     * when a pin is updated.
     *
     * @param  Pin  $pin  The pin instance that was updated.
     */
    public function updated(Pin $pin): void
    {
        $this->clearAllBoundsCache();
        Cache::forget('pins_campaign_'.$pin->campaign_id);
        Cache::forget('pins_user_'.$pin->user_id);
    }

    /**
     * Handle the Pin "deleted" event.
     *
     * Clears the bounds cache and specific caches related to the pin's campaign and user
     * when a pin is deleted.
     *
     * @param  Pin  $pin  The pin instance that was deleted.
     */
    public function deleted(Pin $pin): void
    {
        $this->clearAllBoundsCache();
        Cache::forget('pins_campaign_'.$pin->campaign_id);
        Cache::forget('pins_user_'.$pin->user_id);
    }

    /**
     * Handle the Pin "restored" event.
     *
     * Clears the bounds cache and specific caches related to the pin's campaign and user
     * when a deleted pin is restored.
     *
     * @param  Pin  $pin  The pin instance that was restored.
     */
    public function restored(Pin $pin): void
    {
        $this->clearAllBoundsCache();
        Cache::forget('pins_campaign_'.$pin->campaign_id);
        Cache::forget('pins_user_'.$pin->user_id);
    }

    /**
     * Handle the Pin "force deleted" event.
     *
     * Clears the bounds cache and specific caches related to the pin's campaign and user
     * when a pin is permanently deleted.
     *
     * @param  Pin  $pin  The pin instance that was force deleted.
     */
    public function forceDeleted(Pin $pin): void
    {
        $this->clearAllBoundsCache();
        Cache::forget('pins_campaign_'.$pin->campaign_id);
        Cache::forget('pins_user_'.$pin->user_id);
    }

    /**
     * Clear all bounds-related cache.
     *
     * Removes cached data for all bounds keys and the bounds keys cache itself.
     */
    protected function clearAllBoundsCache(): void
    {
        $keys = Cache::get('pins_bounds_keys', []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget('pins_bounds_keys');
    }
}
