<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            '*.client' => 'required|integer',
            '*.sms_type' => 'required|string',
            '*.smsqty' => 'required|float',
            '*.price' => 'required|float',
            '*.validity_date' => 'required|string',
            '*.invoice_vat' => 'required|integer',
            '*.invoice_date' => 'required',
            '*.paymentoption' => 'required',
            '*.remarks' => 'required|string',
        ];
    }
}
