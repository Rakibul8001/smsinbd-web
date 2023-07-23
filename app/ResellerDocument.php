<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResellerDocument extends Model
{
    protected $fillable = [
        'nid',
        'reseller_id',
        'application',
        'customppphoto',
        'tradelicence',
        'isVerified',
        'root_user_id',
        'manager_id',
        'reseller_id',
        'user_type'
    ];


    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }
}
