<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'account_head_id',
        'account_parent_id',
        'amount_dr',
        'amount_cr',
        'user_id',
        'voucher_owner',
        'voucher_owner_id',
        'voucher_id',
        'transection_id',
        'voucher_date',
        'voucher_create_origin'
    ];

    public function acchead()
    {
        return $this->belongsTo(AccountHead::class,'account_head_id','id');
    }

    public function parenthead()
    {
        return $this->belongsTo(AccountHead::class,'account_parent_id','id');
    }


}
