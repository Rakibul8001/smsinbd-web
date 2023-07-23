<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Core\Users\ClientInterface;
use Illuminate\Support\Facades\Auth;
use App\Core\ClientSenderid\ClientSenderid;
use App\Core\Senderid\SenderId;
use App\Http\Requests\ClientSenderidRequest;
use stdClass;
use DB;
use Carbon\Carbon;

class ClientSenderidController extends Controller
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
        $this->middleware('auth:root,manager');

        $this->clientsenderid = $clientsenderid;
        $this->client = $client;
        $this->smssenderid = $smssenderid;
    }


    public function assignSenderIDsToClient(Request $request) {
      
        if (Auth::guard('root')->check())
        {
            $owner = Auth::guard('root')->user();
            $usertype = 'root';
            
        } else {
            $owner = Auth::guard('manager')->user();
            $usertype = 'manager';
            
        }

        foreach($request->sms_sender_id as $senderid) {

            $usersenderid = $this->clientsenderid->assignSenderIdToClient([
                'user_id' => $request->userid,
                'sms_sender_id' => $senderid,
                'status' => '1',
                'default' => '0',
                'created_by' => $owner->id,
                'updated_by' => $owner->id,
                'user_type' => $usertype,
            ]);

            if (Auth::guard('manager')->check()) {

                $manager = Auth::guard('manager')->user()->name;

                DB::table("staff_activities")
                    ->insert([
                        'manager_id' => Auth::guard('manager')->user()->id,
                        'activity_name' => 'Assign Sender ID',
                        'activity_type' => 'Insert',
                        'activity_desc' => "Manager {$manager} assign senderid to {$usersenderid->user_id}",
                        'record_id' => $usersenderid->id,
                        'invoice_val' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
            }
            
        }

        return back()->with('msg','Senderid successfully assigned'); 
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
                $owner = Auth::guard('manager')->user();
                $usertype = 'manager';
                
            }

            foreach($request->activeclients as $user) {

                $usersenderid = $this->clientsenderid->assignSenderIdToClient([
                    'user_id' => $user,
                    'sms_sender_id' => $request->client_sender_id,
                    'status' => '1',
                    'default' => '0',
                    'created_by' => $owner->id,
                    'updated_by' => $owner->id,
                    'user_type' => $usertype,
                ]);

                if (Auth::guard('manager')->check()) {

                    $manager = Auth::guard('manager')->user()->name;

                    DB::table("staff_activities")
                        ->insert([
                            'manager_id' => Auth::guard('manager')->user()->id,
                            'activity_name' => 'Assign Sender ID',
                            'activity_type' => 'Insert',
                            'activity_desc' => "Manager {$manager} assign senderid to {$usersenderid->user_id}",
                            'record_id' => $usersenderid->id,
                            'invoice_val' => 0,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                }
                
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
                            ->activeClients();

        $clients = $this->clientsenderid
                        ->getClientsWithoutSenderId(
                            $senderid
                        );

        $clientsenderid = $this->clientsenderid
                                ->getClientWithSenderId(
                                    $senderid
                                );

        $clientsenderids = $this->clientsenderid
                                ->getAllClientsWithSenderId(
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
                    ->deleteClientAssignedSenderId(
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
