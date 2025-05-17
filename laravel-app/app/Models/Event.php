<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['name','is_active','description'];

    public function gamePins()
    {
        return $this->hasMany(GamePin::class);
    }
}
