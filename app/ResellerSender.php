<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResellerSender extends Model
{
    protected $fillable = [
        'reseller_id',
        'sms_sender_id',
        'status',
        'default',
        'created_by',
        'updated_by',
        'user_type'
    ];

    public function senderResellers()
    {
        return $this->belongsTo(SmsSender::class,'sms_sender_id','id');
    }

    public function reseller()
    {
        return $this->belongsTo(Reseller::class,'reseller_id','id');
    }

    public function userAssignedSenderId()
    {
        return $this->hasMany(UserSender::class, 'sms_sender_id','id')->where('user_type','reseller');
    }

}
