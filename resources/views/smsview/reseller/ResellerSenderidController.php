<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Core\Users\ResellerInterface;
use Illuminate\Support\Facades\Auth;
use App\Core\ResellerSenderid\ResellerSenderid;
use App\Core\Senderid\SenderId;
use App\Http\Requests\ResellerSenderidRequest;
use stdClass;
use DB;
use Carbon\Carbon;

class ResellerSenderidController extends Controller
{
    /**
     * Client Senderid service
     *
     * @var App\Core\ClientSenderid\ClientSenderidDetails
     */
    protected $resellersenderid;

    /**
     * Client service
     *
     * @var App\Core\Users\ResellerRepository
     */
    protected $reseller;

    /**
     * SmsSenderid service
     *
     * @var App\Core\Senderid\SenderidDetails
     */
    protected $smssenderid;

    public function __construct(
        ResellerSenderid $resellersenderid,
        ResellerInterface $reseller,
        SenderId $smssenderid
    )
    {
        $this->middleware('auth:root,manager');

        $this->resellersenderid = $resellersenderid;
        $this->reseller = $reseller;
        $this->smssenderid = $smssenderid;
    }

    /**
     * Assign senderid to a client
     *
     * @param ClientSenderidRequest $request
     * @return void
     */
    public function assignSenderIdToReseller(ResellerSenderidRequest $request)
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

                $usersenderid = $this->resellersenderid->assignSenderIdToReseller([
                    'reseller_id' => $user,
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
                            'activity_name' => 'Assign Reseller Sender ID',
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
     * Assign senderid to a client
     *
     * @param ClientSenderidRequest $request
     * @return void
     */
    public function assignSenderIdsToReseller(Request $request)
    {

        return $request->sms_sender_id;
        if (Auth::guard('root')->check())
        {
            $owner = Auth::guard('root')->user();
            $usertype = 'root';
            
        } else {
            $owner = Auth::guard('manager')->user();
            $usertype = 'manager';
            
        }

        foreach($request->sms_sender_id as $senderid) {

            $usersenderid = $this->resellersenderid->assignSenderIdToReseller([
                'reseller_id' => $request->userid,
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
                        'activity_name' => 'Assign Reseller Sender ID',
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
     * Load client list of senderid
     *
     * @param int $senderid
     * @return void
     */
    public function loadAssignedSenderId($senderid)
    {
        $activeclients = $this->reseller
                            ->activeResellers();

        $clients = $this->resellersenderid
                        ->getResellerWithoutSenderId(
                            $senderid
                        );

        $clientsenderid = $this->resellersenderid
                                ->getResellerWithSenderId(
                                    $senderid
                                );

        $clientsenderids = $this->resellersenderid
                                ->getAllResellersWithSenderId(
                                    $senderid
                                );

        $smssenderidinfo = $this->smssenderid
                                ->getSenderIdById(
                                    $senderid
                                );

        return view('smsview.smssenderid.load-assigned-reseller-senderid',compact(
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
    public function deleteResellerAssignedSenderId($assigned_user_senderid, $senderid)
    {
        return $this->resellersenderid
                    ->deleteResellerAssignedSenderId(
                        $assigned_user_senderid, 
                        $senderid
                    );
    }

    public function resellerSenderidList(Request $request)
    {
        $resellerid = !empty($request->resellerid) ? $request->resellerid : Auth::guard('reseller')->user()->id;
        
        $senderids = $this->resellersenderid
                    ->showResellerAssignedSenderId(
                        $resellerid 
                    );

        $senderid = [];

        foreach($senderids as $sender)
        {
            $senderid[] = $sender->sender_name;
        }

        return $senderid;
        
    }
}
