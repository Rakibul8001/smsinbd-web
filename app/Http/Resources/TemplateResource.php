<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TemplateResource extends JsonResource
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
            'template_title' => $this->template_title,
            'template_desc' => $this->template_desc,
            'content_file' => $this->content_file,
            'user_id' => $this->user_id,
            'user_type' => $this->user_type,
            'username' => $this->when($this->user_type == 'client',$this->user->name),
            'rootusername' => $this->when($this->user_type == 'root',$this->root->name),
            'status' => $this->when($this->status == 1, 'Active'),
            'status' => $this->when($this->status == 0, 'Inactive'),
        ];
    }
}
