<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            'account_head_id' => $this->account_head_id,
            'account_parent_id' => $this->account_parent_id,
            'amount_dr' => $this->amount_dr,
            'amount_cr' => $this->amount_cr,
            'user_id' => $this->user_id,
            'voucher_owner' => $this->voucher_owner,
            'voucher_owner_id' => $this->voucher_owner_id,
            'voucher_id' => $this->voucher_id,
            'transection_id' => $this->transection_id,
            'voucher_date' => $this->voucher_date,
            'voucher_create_origin' => $this->voucher_create_origin
        ];
    }
}
