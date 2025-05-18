<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Campaign
 *
 * Represents a campaign in the application. A campaign can have multiple pins
 * associated with it and contains details such as its name, active status, and description.
 */
class Campaign extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'is_active', 'description'];

    /**
     * Get the pins associated with the campaign.
     *
     * This defines a one-to-many relationship between the Campaign and Pin models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pins()
    {
        return $this->hasMany(Pin::class);
    }
}
