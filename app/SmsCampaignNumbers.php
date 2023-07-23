<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsCampaignNumbers extends Model
{
    protected $fillable = [
        'campaign_id',
        'number',
        'operator',
        'error_id',
        'status',
        'active'
    ];
}
