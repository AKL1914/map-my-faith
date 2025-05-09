<?php

namespace App\Observers;

use App\Models\Campaign;
use Illuminate\Support\Facades\Cache;

class CampaignObserver
{
    public function created(Campaign $campaign): void
    {
        $this->clearCampaignCache();
    }

    public function updated(Campaign $campaign): void
    {
        $this->clearCampaignCache();
    }

    public function deleted(Campaign $campaign): void
    {
        $this->clearCampaignCache();
    }

    public function restored(Campaign $campaign): void
    {
        $this->clearCampaignCache();
    }

    public function forceDeleted(Campaign $campaign): void
    {
        $this->clearCampaignCache();
    }

    protected function clearCampaignCache(): void
    {
        Cache::forget('all_campaigns');
        Cache::forget('active_campaign_id');

        // Optionally clear pins_bounds_keys if needed
        $keys = Cache::get('pins_bounds_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        Cache::forget('pins_bounds_keys');

    }
}
