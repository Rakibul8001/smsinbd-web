<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GateWayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'gateway_name' => $this->gateway_name,
            'operator_id' => $this->operator_id,
            'user' => $this->user,
            'password' => $this->password,
            'api_url' => $this->api_url,
            'status' => $this->status,
            'created_by'=> $this->created_by,
            'updated_by' => $this->updated_by,
            'operator' => $this->operator 
        ];
    }
}
