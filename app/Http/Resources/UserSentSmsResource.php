<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSentSmsResource extends JsonResource
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
            'remarks' => $this->remarks,
            'user_id' => $this->user_id,
            'user_sender_id' => $this->sms_sender_id,
            'to_number' => $this->to_number,
            'sms_type' => $this->sms_type,
            'sms_catagory' => $this->sms_category,
            'sms_content' => $this->sms_content,
            'number_of_sms' => $this->number_of_sms,
            'total_contacts' => $this->total_contacts,
            'send_type' => $this->send_type,
            'contact_group_id' => $this->contact_group_id,
            'status' => $this->status,
            'submitted_at' => $this->submitted_at,
            'usersenderid' => $this->usersenderid
        ];
    }
}
