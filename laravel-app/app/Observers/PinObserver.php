<?php

namespace App\Observers;

use App\Models\Pin;
use Illuminate\Support\Facades\Cache;

class PinObserver
{
    /**
     * Handle the Pin "created" event.
     */
    public function created(Pin $pin): void
    {
        Cache::forget('all_pins'); // if you cache all pins elsewhere
        Cache::flush(); // Clear all, or use a better scoped approach
    }

    /**
     * Handle the Pin "updated" event.
     */
    public function updated(Pin $pin): void
    {
        Cache::forget('all_pins');
        Cache::flush();
    }

    /**
     * Handle the Pin "deleted" event.
     */
    public function deleted(Pin $pin): void
    {
        Cache::forget('all_pins');
        Cache::flush();
    }

    /**
     * Handle the Pin "restored" event.
     */
    public function restored(Pin $pin): void
    {
        Cache::forget('all_pins');
        Cache::flush();
    }

    /**
     * Handle the Pin "force deleted" event.
     */
    public function forceDeleted(Pin $pin): void
    {
        Cache::forget('all_pins');
        Cache::flush();
    }
}
