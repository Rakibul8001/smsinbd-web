<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductSale extends Model
{
    protected $fillable = [
        'user_id',
        'transection_id',
        'sms_category',
        'user_type',
        'qty',
        'qty_return',
        'rate',
        'price',
        'validity_period',
        'invoice_vat',
        'vat_amount',
        'invoice_date',
        'invoice_owner_type',
        'invoice_owner_id',
    ];
}
