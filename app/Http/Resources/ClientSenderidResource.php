<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientSenderidResource extends JsonResource
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
            'sms_sender_id' => $this->sms_sender_id,
            'sender_name' => $this->senderClients->sender_name,
            'status' => $this->status,
            'default' => $this->default,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'user_type' => $this->user_type,
            'client' => $this->client,
            'senderClients' => $this->senderClients,
        ];
    }
}
