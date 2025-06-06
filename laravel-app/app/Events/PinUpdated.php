<?php

namespace App\Events;

use App\Models\Pin;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PinUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(Pin $pin)
    {
        $this->pin = $pin;
    }
}
