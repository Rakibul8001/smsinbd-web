<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = "user_templates";

    protected $fillable = [
        'template_title',
        'template_desc',
        'content_file',
        'user_id',
        'user_type',
        'status'
    ];

    public function root()
    {
        return $this->belongsTo(RootUser::class,'user_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

}
