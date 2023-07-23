<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResellerResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'phone' => $this->phone,
            'company' => $this->company,
            'root_user_id' => $this->user_manager_id,
            'manager_id' => $this->user_manager_id,
            'address' => $this->address,
            'country' => $this->country,
            'city' => $this->city,
            'state' => $this->state,
            'created_from' => $this->create_from,
            'create_by' => $this->created_by,
        ];
    }
}
