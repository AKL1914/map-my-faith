<?php

namespace App\Observers;

use App\Jobs\FetchSuburbFromCoordinates;
use App\Models\Pin;
use Illuminate\Support\Facades\Cache;

class PinObserver
{
    public function created(Pin $pin): void
    {
        FetchSuburbFromCoordinates::dispatch($pin->id);
        $this->clearAllBoundsCache();
        Cache::forget('all_pins');
    }

    public function updated(Pin $pin): void
    {
        $this->clearAllBoundsCache();
        Cache::forget('pins_campaign_' . $pin->campaign_id);
        Cache::forget('pins_user_' . $pin->user_id);
    }

    public function deleted(Pin $pin): void
    {
        $this->clearAllBoundsCache();
        Cache::forget('pins_campaign_' . $pin->campaign_id);
        Cache::forget('pins_user_' . $pin->user_id);
    }

    public function restored(Pin $pin): void
    {
        $this->clearAllBoundsCache();
        Cache::forget('pins_campaign_' . $pin->campaign_id);
        Cache::forget('pins_user_' . $pin->user_id);
    }

    public function forceDeleted(Pin $pin): void
    {
        $this->clearAllBoundsCache();
        Cache::forget('pins_campaign_' . $pin->campaign_id);
        Cache::forget('pins_user_' . $pin->user_id);
    }

    protected function clearAllBoundsCache(): void
    {
        $keys = Cache::get('pins_bounds_keys', []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget('pins_bounds_keys');
    }
}
