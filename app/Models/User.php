<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'country',
        'city',
        'mobile',
        'role',
        'mac_address',
        'device_name',
        'parent_id',
        'reference',
        'temporary_family_tie',
        'rtl',
        'status',
        'gender',
        'language',
        'otp',
        'account_deleted',
        'isMapAccess',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public static function otpGenerate($user_id, $model_name, $sending_mode, $user_parent_id)
    {
        $otp = mt_rand(1000, 9999);
        $otp_generated = Otp::create([
            'user_id' => $user_id,
            'user_parent_id' => $user_parent_id,
            'otp_code' => $otp,
            'sending_mode' => $sending_mode,
            'model_name' => $model_name,
        ]);
        return $otp_generated;
    }
}
