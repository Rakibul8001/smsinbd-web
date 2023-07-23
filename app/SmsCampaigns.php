<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsCampaigns extends Model
{
    protected $fillable = [
        'campaign_name',
        'campaign_description',
        'user_id',
        'sender_id',
        'is_unicode',
        'category',
        'content',
        'sms_qty',
        'total_numbers',
        'sent_through',
        'is_scheduled',
        'scheduled_time',
        'status',
        'created_at',
        'updated_at',
        'active'
    ];

    public function getSenderId()
    {
        return $this->belongsTo(SenderidMaster::class,'sender_id','id');
    }
}
