<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'a_one' => $this->a_one,
            'a_two' => $this->a_two,
            'country_code' => $this->country_code,
            'country_name' => $this->country_name,
            'default_country' => $this->default_country
        ];
    }
}
