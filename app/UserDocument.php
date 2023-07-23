<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    protected $fillable = [
        'nid',
        'user_id',
        'application',
        'customppphoto',
        'tradelicence',
        'isVerified',
        'root_user_id',
        'manager_id',
        'reseller_id',
        'user_type'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
