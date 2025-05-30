<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Event
 *
 * Represents an event in the application. An event can have multiple game pins
 * associated with it and contains details such as its name, active status, and description.
 */
class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'is_active', 'description'];

    /**
     * Get the game pins associated with the event.
     *
     * This defines a one-to-many relationship between the Event and GamePin models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gamePins()
    {
        return $this->hasMany(GamePin::class);
    }
}
