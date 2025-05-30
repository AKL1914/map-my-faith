<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Pin
 *
 * Represents a pin in the application. A pin is associated with a user and a campaign,
 * and contains details such as its latitude, longitude, acceptance status, and notes.
 */
class Pin extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',      // The ID of the user associated with the pin.
        'latitude',     // The latitude of the pin's location.
        'longitude',    // The longitude of the pin's location.
        'campaign_id',  // The ID of the campaign associated with the pin.
        'is_accepted',  // A boolean indicating whether the pin has been accepted.
        'notes',        // Additional notes or comments about the pin.
    ];

    /**
     * Get the user associated with the pin.
     *
     * This defines a many-to-one relationship between the Pin and User models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the campaign associated with the pin.
     *
     * This defines a many-to-one relationship between the Pin and Campaign models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
