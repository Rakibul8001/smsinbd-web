<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserFromRequest extends FormRequest
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
        if ($this->request->get('usertype') == 'root' && $this->request->get('paneltype') == 'admin panel') {
            return [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:root_users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
                'usertype' => ['required','string'],
                'phone' => ['required', 'string', 'unique:root_users'],
                'country' => ['required','string'],
                'address' => ['required','string'],
                'city' => ['required','string'],
            ];
        }else if ($this->request->get('usertype') == 'manager' && $this->request->get('paneltype') == 'admin panel') {
            return [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:managers'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
                'usertype' => ['required','string'],
                'phone' => ['required', 'string', 'unique:managers'],
                'country' => ['required','string'],
                'address' => ['required','string'],
                'city' => ['required','string'],
            ];
        } else if ($this->request->get('usertype') == 'reseller' && $this->request->get('paneltype') == 'admin panel') {
            return [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:resellers'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
                'usertype' => ['required','string'],
                'phone' => ['required', 'string', 'unique:resellers'],
                'country' => ['required','string'],
                'address' => ['required','string'],
                'city' => ['required','string'],
            ];
        } else if ($this->request->get('usertype') == 'client' && $this->request->get('paneltype') == 'web') {
            return [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
                'usertype' => ['required','string'],
                'phone' => ['required', 'string', 'unique:users'],
                'country' => ['required','string'],
                'address' => ['required','string']
            ];
        } else {
            return [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
                'usertype' => ['required','string'],
                'phone' => ['required', 'string', 'unique:users'],
                'country' => ['required','string'],
                'address' => ['required','string'],
            ];
        }
        
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Name is Required',
            'email.required'  => 'Email is Required',
            'password.required' => 'Password is Required',
            'phone.required' => 'Phone number is Required',
            'usertype.required' => 'User type is required',
            'company.required' => 'Company is required',
            'country.required' => 'Country is required',
            'address.required' => 'Address is requeired',
            'city.required' => 'City is requeired'
        ];
    }
}
