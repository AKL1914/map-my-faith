<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * Class Campaign
 * 
 * Represents a campaign in the application. A campaign can have multiple pins
 * associated with it and contains details such as its name, active status, and description.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pin> $pins
 * @property-read int|null $pins_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereUpdatedAt($value)
 */
	class Campaign extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Event
 * 
 * Represents an event in the application. An event can have multiple game pins
 * associated with it and contains details such as its name, active status, and description.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $is_active
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GamePin> $gamePins
 * @property-read int|null $game_pins_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUserId($value)
 */
	class Event extends \Eloquent {}
}

namespace App\Models{
/**
 * Class GamePin
 * 
 * Represents a game pin in the application. A game pin is associated with a user
 * and an event, and contains details such as its latitude, longitude, and whether
 * it has been taken.
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $event_id
 * @property string $latitude
 * @property string $longitude
 * @property int $is_taken
 * @property string|null $distance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Event|null $event
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePin query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePin whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePin whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePin whereIsTaken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePin whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePin whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePin whereUserId($value)
 */
	class GamePin extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Pin
 * 
 * Represents a pin in the application. A pin is associated with a user and a campaign,
 * and contains details such as its latitude, longitude, acceptance status, and notes.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $latitude
 * @property string $longitude
 * @property string|null $notes
 * @property int $is_accepted
 * @property string|null $suburb
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $campaign_id
 * @property-read \App\Models\Campaign|null $campaign
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\PinFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pin query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pin whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pin whereIsAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pin whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pin whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pin whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pin whereSuburb($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pin whereUserId($value)
 */
	class Pin extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $value
 * @property string|null $note
 * @property bool $enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereValue($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * Class User
 * 
 * Represents a user in the application. A user can have multiple pins associated with them
 * and contains details such as their name, email, password, and additional attributes like
 * Google ID, admin status, activation status, area, and group.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property int $is_admin
 * @property int $is_activated
 * @property int|null $area
 * @property int|null $group
 * @property string|null $cfo
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pin> $pins
 * @property-read int|null $pins_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \NotificationChannels\WebPush\PushSubscription> $pushSubscriptions
 * @property-read int|null $push_subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActivated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

