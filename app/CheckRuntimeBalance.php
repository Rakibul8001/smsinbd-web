<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckRuntimeBalance extends Model
{
    protected $table = 'check_runtime_balance';

    protected $fillable = [
        'userid',
        'ischecked'
    ];
}
