<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCountSmsResource extends JsonResource
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
            'user_id' => $this->user_id,
            'sms_count' => $this->sms_count,
            'campaing_name' => $this->campaing_name,
            'month_name' => $this->month_name,
            'year_name' => $this->year_name,
            'owner_id' => $this->owner_id,
            'owner_type' => $this->owner_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
