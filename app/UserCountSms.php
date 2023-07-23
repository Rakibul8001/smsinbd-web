<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCountSms extends Model
{
    protected $table = 'user_count_smses';

    protected $fillable = [
        'user_id',
        'sms_count',
        'campaing_name',
        'sms_category',
        'month_name',
        'year_name',
        'owner_id',
        'owner_type'
    ];
}
