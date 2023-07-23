<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SenderidResellers extends Model
{
    protected $table = 'senderid_resellers';
    protected $fillable = [
        'senderid',
        'reseller',
        'user_type',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'active',
    ];

    public function getUsersOfSenderid()
    {
        return $this->hasMany(User::class,'senderid','id');
    }

    public function getResellerOfSenderid()
    {
        return $this->belongsTo(Reseller::class,'reseller','id');
    }

    public function getSenderid()
    {
        return $this->belongsTo(SenderidMaster::class,'senderid', 'id');
    }

}