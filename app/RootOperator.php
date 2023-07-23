<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RootOperator extends Model
{
    
    public function operatorId()
    {
        return $this->belongsTo(Operator::class);
    }

    public function gatewayCreatedBy()
    {
        return $this->belongsTo(RootUser::class,'gateway_careated_by','id');
    }

    public function gatewayUpdatedBy()
    {
        return $this->belongsTo(RootUser::class,'gateway_updated_by','id');
    }
}
