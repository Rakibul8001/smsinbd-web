<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SmsAdminSettingsRequest extends FormRequest
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
            'site_name' => 'required|string|max:100',//$request->site_name,
            'site_slogan' => 'required|string|max:200',//$request->site_slogan,
            'address' => 'required|string|max:200',//$request->address,
            'email' => 'required|string|email',//$request->email,
            'order_email' => 'required|string|email',//$request->order_email,
            'email_from' => 'required|string|email',//$request->email_from,
            'contact_phone' => 'required|string|max:14',//$request->contact_phone,
            
            'about_site' => 'required|string|max:200',//$request->about_site,
            'max_audio_file_size' => 'required|string',//$request->max_audio_file_size,
            'max_text_sms_limit' => 'required|string',//$request->max_text_sms_limit,
            'max_voice_sms_limit' => 'required|string',//$request->max_voice_sms_limit,
            'text_limit_campaing' => 'required|string',//$request->text_limit_campaing,
            'voice_limit_campaing' => 'required|string',//$request->voice_limit_campaing,
        ];
    }
}
