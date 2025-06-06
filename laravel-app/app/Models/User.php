<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;

/**
 * Class User
 *
 * Represents a user in the application. A user can have multiple pins associated with them
 * and contains details such as their name, email, password, and additional attributes like
 * Google ID, admin status, activation status, area, and group.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasPushSubscriptions, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',         // The name of the user.
        'email',        // The email address of the user.
        'password',     // The hashed password of the user.
        'google_id',    // The Google ID of the user (if applicable).
        'is_admin',     // A boolean indicating whether the user is an admin.
        'is_activated', // A boolean indicating whether the user is activated.
        'area',         // The area associated with the user.
        'group',        // The group associated with the user.
        'cfo',        // The group associated with the user.
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',         // The hashed password of the user.
        'remember_token',   // The token used to remember the user session.
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime', // Casts the email verification timestamp to a DateTime object.
    ];

    /**
     * Get the pins associated with the user.
     *
     * This defines a one-to-many relationship between the User and Pin models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pins()
    {
        return $this->hasMany(Pin::class);
    }
}
