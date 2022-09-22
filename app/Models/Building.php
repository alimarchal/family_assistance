<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'building_name',
        'building_type',
        'coordinates_type',
        'lat_long',
        'mobile',
        'telephone',
        'status',
    ];
}
