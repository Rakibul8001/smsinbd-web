<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SmsSenderIdRequest extends FormRequest
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

    public function attributes()
    {

        return [
            'sender_name' => 'Sms Sender name',
            'created_by' => 'created By',
            'updated_by' => 'updated By',
            'sendertype' => 'Sms Sender Type',
            'user' => 'Talitalk user',
            'password' => 'Talitalk Password'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->request->get('sendertype') == 'general')
        {
            return [
                'sender_name' => 'required|string',
            ];
        }

        if ($this->request->get('sendertype') == 'teletalk') {
            return [
                'sender_user' => 'required|string',
                'sender_password' => 'required|string'
            ];
        }

    }
}
