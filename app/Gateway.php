<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    protected $fillable = [
        'gateway_name',
        'operator_id',
        'user',
        'password',
        'api_url',
        'status',
        'created_by',
        'updated_by'
    ];

    public function operator()
    {
        return $this->belongsTo(Operator::class,'operator_id','id');
    }

    public function rootUser()
    {
        return $this->belongsTo(RootUser::class,'created_by','id');
    }
}
