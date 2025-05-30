<?php

namespace App\Observers;

use App\Models\Campaign;
use Illuminate\Support\Facades\Cache;

/**
 * Class CampaignObserver
 *
 * Observes the lifecycle events of the Campaign model and performs actions such as
 * clearing the cache when a campaign is created, updated, deleted, restored, or force deleted.
 */
class CampaignObserver
{
    /**
     * Handle the Campaign "created" event.
     *
     * Clears the campaign-related cache when a new campaign is created.
     *
     * @param Campaign $campaign The campaign instance that was created.
     * @return void
     */
    public function created(Campaign $campaign): void
    {
        $this->clearCampaignCache();
    }

    /**
     * Handle the Campaign "updated" event.
     *
     * Clears the campaign-related cache when a campaign is updated.
     *
     * @param Campaign $campaign The campaign instance that was updated.
     * @return void
     */
    public function updated(Campaign $campaign): void
    {
        $this->clearCampaignCache();
    }

    /**
     * Handle the Campaign "deleted" event.
     *
     * Clears the campaign-related cache when a campaign is deleted.
     *
     * @param Campaign $campaign The campaign instance that was deleted.
     * @return void
     */
    public function deleted(Campaign $campaign): void
    {
        $this->clearCampaignCache();
    }

    /**
     * Handle the Campaign "restored" event.
     *
     * Clears the campaign-related cache when a deleted campaign is restored.
     *
     * @param Campaign $campaign The campaign instance that was restored.
     * @return void
     */
    public function restored(Campaign $campaign): void
    {
        $this->clearCampaignCache();
    }

    /**
     * Handle the Campaign "force deleted" event.
     *
     * Clears the campaign-related cache when a campaign is permanently deleted.
     *
     * @param Campaign $campaign The campaign instance that was force deleted.
     * @return void
     */
    public function forceDeleted(Campaign $campaign): void
    {
        $this->clearCampaignCache();
    }

    /**
     * Clear the campaign-related cache.
     *
     * Removes cached data for all campaigns, the active campaign ID, and optionally
     * clears cached keys related to pin bounds.
     *
     * @return void
     */
    protected function clearCampaignCache(): void
    {
        Cache::forget('all_campaigns'); // Clears the cache for all campaigns.
        Cache::forget('active_campaign_id'); // Clears the cache for the active campaign ID.

        // Optionally clear pins_bounds_keys if needed.
        $keys = Cache::get('pins_bounds_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key); // Clears each key in the pins_bounds_keys cache.
        }
        Cache::forget('pins_bounds_keys'); // Clears the pins_bounds_keys cache itself.
    }
}
