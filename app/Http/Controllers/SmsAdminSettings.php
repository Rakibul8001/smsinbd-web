<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SmsAdminSettingsRequest;
use App\Core\SmsAdminSettings\SmsAdminSettings AS AdminSettings;

class SmsAdminSettings extends Controller
{
    
    /**
     * SmsAdminSettings Settings
     *
     * @var App\Core\SmsAdminSettings\SmsAdminSettingsDetails
     */
    protected $settings;

    public function __construct(AdminSettings $settings)
    {
        $this->middleware('auth:root');

        $this->settings = $settings;
    }

    /**
     * Sms Admin Parameters & settings
     *
     * @return void
     */
    public function smsAdminSettings()
    {
        
        $config = $this->settings->showSmsAdminSettings();

	    return view('smsview.rootadmin.smsadmin-settings',compact('config'));
    }

    /**
     * Update SmsAdminSettings Configuration
     *
     * @param Request $request
     * @return void
     */
    public function smsAdminSettingsUpdate(SmsAdminSettingsRequest $request)
    { 

        //return $request->under_maintenence;
        $settings = $this->settings->smsAdminSettingsUpdate([
                'site_name' => $request->site_name,
                'site_slogan' => $request->site_slogan,
                'address' => $request->address,
                'email' => $request->email,
                'order_email' => $request->order_email,
                'email_from' => $request->email_from,
                'contact_phone' => $request->contact_phone,
                'fb_link' => $request->fb_link,
                'twitter_link' => $request->twitter_link,
                'linkedin_link' => $request->linkedin_link,
                'recaptcha_site_key' => $request->recaptcha_site_key,
                'about_site' => $request->about_site,
                'under_maintenence' => $request->under_maintenence,
                'maintenence_messsage' => $request->maintenence_messsage,
                'max_audio_file_size' => $request->max_audio_file_size,
                'max_text_sms_limit' => $request->max_text_sms_limit,
                'max_voice_sms_limit' => $request->max_voice_sms_limit,
                'text_limit_campaing' => $request->text_limit_campaing,
                'voice_limit_campaing' => $request->voice_limit_campaing,
                'ssl_comm_user' => $request->ssl_comm_user,
                'ssl_comm_password' => $request->ssl_comm_password
        ]);

	    return back()->with('msg',$settings->original['msg']);
    }
}