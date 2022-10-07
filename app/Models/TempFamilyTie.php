<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempFamilyTie extends Model
{
    use HasFactory;

    protected $fillable = [
        'expiry_date_time',
        'my_id',
        'head_id',
        'accepted',
        'tie_code',
        'untie_request',
    ];
}
