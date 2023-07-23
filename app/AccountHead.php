<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountHead extends Model
{
    protected $fillable = [
        'acc_head',
        'parent_id',
        'status',
        'created_by',
        'updated_by',
        'user_type',
        'account_type'
    ];

    public function parent()
    {
        return $this->belongsTo(static::class,'parent_id','id');
    }

    public function child()
    {
        return $this->hasMany(static::class, 'parent_id','id');
    }

    public function createdby()
    {
        return $this->belongsTo(RootUser::class,'created_by','id');
    }

    public function updatedby()
    {
        return $this->belongsTo(RootUser::class, 'updated_by','id');
    }

    public function getAccHeadAttribute($value)
    {
        return ucwords($value);
    }
}
