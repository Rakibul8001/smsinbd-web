<?php

namespace App\Http\Controllers;


use App\Core\Senderid\SenderId;
use Illuminate\Http\Request;

class ResellerClientSenderidController extends Controller
{
    /**
     * Sender ID Service
     *
     * @var App\Core\Semderid\SenderidDetails
     */
    protected $senderid;

    
    public function __construct(
        SenderId $senderid
    )
    {
        $this->middleware('auth:root,reseller,manager');

        $this->senderid = $senderid;
    }

    

    
    public function showSenderIdForResellerClient(){

        return view('smsview.smssenderid.show-senderid-for-reseller-client');
    }


    public function renderSenderId(Request $request)
    {
        return $this->senderid->showResellerSmsSenderId($request->userid);
    }

}
