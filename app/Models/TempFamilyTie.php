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

    protected $appends = ['user'];

    public function getUserAttribute()
    {
        if ($this->my_id == auth()->user()->id) {
            $head_id = $this->head_id;
            return User::find($head_id);
        } elseif ($this->head_id == auth()->user()->id) {
            return User::find($this->my_id);
        }
    }

}
