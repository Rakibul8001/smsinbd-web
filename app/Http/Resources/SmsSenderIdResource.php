<?php

namespace App\Http\Resources;

use App\Operators;
use App\SmsSender;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class SmsSenderIdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        // dd($request);

        return [
            'id' => $this->id,
            'sender_name' => $this->sender_name,
            'operator_id' => $this->operator_id,
            'operator' => Operators::where('id', $this->operator_id)->first(),
            'status' => $this->status,
            'default' => $this->default,
            'gateway_info' => $this->gateway_info,
            'rotation_gateway_info' => $this->rotation_gateway_info,
            'user' => $this->user,
            'password' => $this->password,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'senderHasClients' => DB::table('users')->get()
                                   
        ];
    }
}
