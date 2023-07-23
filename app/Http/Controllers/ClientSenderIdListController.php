<?php

namespace App\Http\Controllers;

use App\Core\ClientSenderid\ClientSenderid;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClientSenderIdListController extends Controller
{
    protected $clientsenderid;

    public function __construct(ClientSenderid $clientsenderid)
    {
        $this->middleware('auth:web,root,manager,reseller');
        $this->clientsenderid = $clientsenderid;
    }

    public function showClientSenderId(User $clientid)
    {
        $data = [];
        $clientsenderids = $this->clientsenderid->showClientSenderId($clientid);

          
        foreach($clientsenderids as $clientsender)
        {
            
            $rowid = 1;
            foreach($clientsender->senderids as $senderid)
            {
                $data['data'][] = [
                    'rowid' => $rowid,
                    'user_sender_rec_id' => $senderid->id,
                    'user_id' => $senderid->user_id,
                    'default' => $senderid->default,
                    'sms_sender_id' => $senderid->sms_sender_id,
                    'clientname' => $senderid->client->name,
                    'sender_rec_id' => $senderid->senderClients->id,
                    'senderid' => $senderid->senderClients->sender_name,
                    'senderid_status' => $senderid->senderClients->status == 1 ? 'Yes' : 'No',
                    'created_at' => $clientsender->created_at->format('Y-m-d H:i:s'),

                ];

                $rowid++;
            }
        }

        return $data;
        
    }

    public function setClientDefaultSenderId(Request $request)
    {
        return $this->clientsenderid->clientDefaultSenderId($request->clientid,$request->senderid);
    }


    public function clientSenderIdList()
    {
        return view('smsview.assignsenderid.show-client-senderid');
    }
}
