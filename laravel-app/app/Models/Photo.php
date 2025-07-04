<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'visible',
    ];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->filename);
    }
}
