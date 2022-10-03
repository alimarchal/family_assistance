<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'family_head_id',
        'lat_long',
    ];


    protected $appends = ['user'];

    public function getUserAttribute()
    {
        return User::find($this->user_id);
    }

}
