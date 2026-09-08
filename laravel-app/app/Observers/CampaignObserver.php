<?php

namespace App\Observers;

use App\Http\Controllers\PinController;
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
     * @param  Campaign  $campaign  The campaign instance that was created.
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
     * @param  Campaign  $campaign  The campaign instance that was updated.
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
     * @param  Campaign  $campaign  The campaign instance that was deleted.
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
     * @param  Campaign  $campaign  The campaign instance that was restored.
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
     * @param  Campaign  $campaign  The campaign instance that was force deleted.
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
     */
    protected function clearCampaignCache(): void
    {
        Cache::forget('all_campaigns'); // Clears the cache for all campaigns.
        Cache::forget('active_campaign_id'); // Clears the cache for the active campaign ID.

        // Bump the bounds-cache generation instead of enumerating and
        // deleting every previously cached bounds key.
        Cache::increment(PinController::BOUNDS_CACHE_VERSION_KEY);
    }
}
