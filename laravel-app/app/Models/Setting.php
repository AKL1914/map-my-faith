<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'name',
        'value',
        'note',
        'enabled',
    ];

    // Optionally cast the "enabled" column to boolean
    protected $casts = [
        'enabled' => 'boolean',
    ];
}
