<?php

namespace App\Events;

use App\Models\Pin;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PinCreated
{
    use Dispatchable;
    use SerializesModels;

    public $pin;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Pin $pin)
    {
        $this->pin = $pin;
    }
}
