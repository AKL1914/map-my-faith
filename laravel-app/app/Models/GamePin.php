<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GamePin
 *
 * Represents a game pin in the application. A game pin is associated with a user
 * and an event, and contains details such as its latitude, longitude, and whether
 * it has been taken.
 */
class GamePin extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',     // The ID of the user associated with the game pin.
        'latitude',    // The latitude of the game pin's location.
        'longitude',   // The longitude of the game pin's location.
        'event_id',    // The ID of the event associated with the game pin.
        'is_taken',    // A boolean indicating whether the game pin has been taken.
    ];

    /**
     * Get the user associated with the game pin.
     *
     * This defines a many-to-one relationship between the GamePin and User models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event associated with the game pin.
     *
     * This defines a many-to-one relationship between the GamePin and Event models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
