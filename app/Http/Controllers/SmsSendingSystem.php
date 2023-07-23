<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Http\Response;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Core\Templates\Template;

use GuzzleHttp\Client;
use DB;

use App\SenderidUsers;
use App\ContactGroup;

class SmsSendingSystem extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:web,root');
    }

    //showing sms sending form
    public function showSendSmsForm()
    {

        $client = Auth::guard('web')->user();
        // dd($client);
        $api_token = $client->api_token;

        $clientSenderids = SenderidUsers::where('user', $client->id)->get();
        // dd($clientSenderids);
        $groups = ContactGroup::where('user_id', $client->id)->get();

        $clientSenderidsArr=[];
        foreach ($clientSenderids as $senderId){
            $clientSenderidsArr[]= $senderId->getSenderid->name;
        }

        // dd($clientSenderidsArr);
        
        return view('smsview.smsSystem.sendSms',compact('client','clientSenderids','clientSenderidsArr', 'groups', 'api_token'));

    }

    //low cost sms sending form
    public function lowCostSmsForm()
    {

        $client = Auth::guard('web')->user();
        $api_token = $client->api_token;

        $groups = ContactGroup::where('user_id', $client->id)->get();
        
        return view('smsview.smsSystem.sendLowCostSms',compact('client', 'groups', 'api_token'));

    }


    public function developerDoc()
    {
        $data = [];

        $client = Auth::guard('web')->user();
        $api_token = $client->api_token;

        $senderIds = SenderidUsers::where('user', $client->id)->get();

        return view('smsview.smsSystem.developer-doc',compact('client','senderIds', 'api_token'));
    }
    
     public function download1()
    {
      $file=public_path("download/AutLtrBlank.docx");
      return response()->download($file);
    }
    
     public function download2()
    {
        $file=public_path("download/AutLtrBlankGP.docx");
        return response()->download($file);
    }
    
     public function download3()
    {
       $file=public_path("download/BTRC-Form-20200001.docx");
       return response()->download($file);
    }
}
