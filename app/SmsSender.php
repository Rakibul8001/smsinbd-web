<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsSender extends Model
{
    protected $fillable = [
        'sender_name',
        'operator_id',
        'status',
        'default',
        'user',
        'password',
        'gateway_info',
        'rotation_gateway_info',
        'created_by',
        'updated_by'
    ];

    public function createdBy()
    {
        return $this->belongsTo(RootUser::class, 'created_by','id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(RootUser::class, 'updated_by','id');
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class,'operator_id','id');
    }

    public function sentSmsSenderId()
    {   
        return $this->hasMany(UserSentSms::class,'sms_sender_id','id');
    }


    // public function senderHasClients()
    // {
    //     return $this->hasMany(UserSender::class,'sms_sender_id','id')->with('client');
    // }
}
