<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = ['name','is_active','description'];

    public function pins()
    {
        return $this->hasMany(Pin::class);
    }
}
