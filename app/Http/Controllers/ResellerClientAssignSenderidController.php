<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Core\Users\ClientInterface;
use Illuminate\Support\Facades\Auth;
use App\Core\ClientSenderid\ClientSenderid;
use App\Core\Senderid\SenderId;
use App\Http\Requests\ClientSenderidRequest;
use stdClass;

class ResellerClientAssignSenderidController extends Controller
{
    /**
     * Client Senderid service
     *
     * @var App\Core\ClientSenderid\ClientSenderidDetails
     */
    protected $clientsenderid;

    /**
     * Client service
     *
     * @var App\Core\Users\ClientRepository
     */
    protected $client;

    /**
     * SmsSenderid service
     *
     * @var App\Core\Senderid\SenderidDetails
     */
    protected $smssenderid;

    public function __construct(
        ClientSenderid $clientsenderid,
        ClientInterface $client,
        SenderId $smssenderid
    )
    {
        $this->middleware('auth:root,reseller');

        $this->clientsenderid = $clientsenderid;
        $this->client = $client;
        $this->smssenderid = $smssenderid;
    }

    /**
     * Assign senderid to a client
     *
     * @param ClientSenderidRequest $request
     * @return void
     */
    public function assignSenderIdToClient(ClientSenderidRequest $request)
    {
        if (is_array($request->activeclients)) {

            if (Auth::guard('root')->check())
            {
                $owner = Auth::guard('root')->user();
                $usertype = 'root';
                
            } else {
                $owner = Auth::guard('reseller')->user();
                $usertype = 'reseller';
                
            }

            foreach($request->activeclients as $user) {

                $this->clientsenderid->assignSenderIdToClient([
                    'user_id' => $user,
                    'sms_sender_id' => $request->client_sender_id,
                    'status' => '1',
                    'default' => '0',
                    'created_by' => $owner->id,
                    'updated_by' => $owner->id,
                    'user_type' => $usertype,
                ]);
                
            }
            
        }

        return back()->with('msg','Senderid successfully assigned'); 
    }

    /**
     * Load client list of senderid
     *
     * @param int $senderid
     * @return void
     */
    public function loadAssignedSenderId($senderid)
    {
        $activeclients = $this->client
                            ->activeResellerClients(Auth::guard('reseller')->user()->id);

        $clients = $this->clientsenderid
                        ->getResellerClientsWithoutSenderId(
                            $senderid
                        );

        $clientsenderid = $this->clientsenderid
                                ->getResellerClientWithSenderId(
                                    $senderid
                                );

        $clientsenderids = $this->clientsenderid
                                ->getAllResellerClientsWithSenderId(
                                    $senderid
                                );

        $smssenderidinfo = $this->smssenderid
                                ->getSenderIdById(
                                    $senderid
                                );

        return view('smsview.smssenderid.load-assigned-senderid',compact(
                                                                        'activeclients',
                                                                        'clientsenderid',
                                                                        'clientsenderids',
                                                                        'clients',
                                                                        'smssenderidinfo'))
                                                                ->with('senderid',$senderid);
    }


    /**
     * Delete Assigned senderid id of a client
     *
     * @param int $assigned_user_senderid
     * @param int $senderid
     * @return void
     */
    public function deleteClientAssignedSenderId($assigned_user_senderid, $senderid)
    {
        return $this->clientsenderid
                    ->deleteResellerClientAssignedSenderId(
                        $assigned_user_senderid, 
                        $senderid
                    );
    }

    public function clientSenderidList(Request $request)
    {
        $clientid = !empty($request->clientid) ? $request->clientid : Auth::guard('web')->user()->id;
        
        $senderids = $this->clientsenderid
                    ->showClientAssignedSenderId(
                        $clientid 
                    );

        $senderid = [];

        foreach($senderids as $sender)
        {
            $senderid[] = $sender->sender_name;
        }

        return $senderid;
        
    }
}
