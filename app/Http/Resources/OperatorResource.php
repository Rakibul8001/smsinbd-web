<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OperatorResource extends JsonResource
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
            'name' => $this->name,
            'prefix' => $this->prefix,
            'type' => $this->type,
            'single_url' => $this->single_url,
            'multi_url' => $this->multi_url,
            'delivery_url' => $this->delivery_url,
            'active' => $this->active,
            'created_by' => $this->created_by,
        ];
    }
}
