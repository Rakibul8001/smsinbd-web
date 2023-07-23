<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\TokenGuard;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'email', 
        'password',
        'phone',
        'company',
        'root_user_id',
        'manager_id',
        'reseller_id',
        'address',
        'country',
        'city',
        'state',
        'created_from',
        'created_by',
        'status',
        'verified',
        'security_code',
        'phone_verified',
        'api_token',
        'live_dipping',
        'otp_allowed'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Get Login User docuemts
     *
     * @return void
     */
    public function documents()
    {
        return $this->hasMany(UserDocument::class);
    }

    public function senderid()
    {
        return $this->hasOne(UserSender::class,'user_id','id');
    }


    public function senderids()
    {
        return $this->hasMany(UserSender::class, 'user_id','id');
    }

}
