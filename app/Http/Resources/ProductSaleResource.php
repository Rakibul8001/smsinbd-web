<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductSaleResource extends JsonResource
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
            'transection_id' => $this->transection_id,
            'sms_category' => $this->sms_category,
            'user_type' => $this->user_type,
            'qty' => $this->qty,
            'qty_return' => $this->qty_return,
            'rate' => $this->rate,
            'price' => $this->price,
            'validity_period' => $this->validity_period,
            'invoice_vat' => $this->invoice_vat,
            'vat_amount' => $this->vat_amount,
            'invoice_date' => $this->invoice_date,
            'invoice_owner_type' => $this->invoice_owner_type,
            'invoice_owner_id' => $this->invoice_owner_id,
        ];
    }
}
