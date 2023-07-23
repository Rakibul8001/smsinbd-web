<?php

namespace App\Http\Controllers;

use App\Core\Users\ClientInterface;
use App\Http\Resources\UserResource;
use App\User;
use App\Reseller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Operators;
use App\OperatorGateways;
use App\SenderidMaster;
use App\SenderidGateways;
use App\SenderidUsers;
use App\SenderidResellers;

class SenderidNewController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:root,manager,reseller');
    }

    public function createSenderid()
    {

        $gsmOperators = Operators::where('type', 'gsm')->where('active', 1)->get();
        $allOperators = Operators::where('active', 1)->get();
        $gateways = OperatorGateways::get();

        return view('smsview.senderid.createSenderid',compact('gateways','gsmOperators', 'allOperators'));
    }

    /**
     * create Sender ID post
     *
     * @param SmsSenderIdRequest $request
     * @return void
     */
    public function createSenderidPost(Request $request)
    {

        if (!$request->sender_name) {
            return back()->with(['errmsg' => 'Invalid Request']);
        }

        $validatedAttributes = $this->validateSenderId();

        $output_operator     = $request->output_operator;
        $gateway            = $request->gateway;
        $associate_sender   = $request->associate_sender;
        $username           = $request->username;
        $password           = $request->password;

        // echo $request->sender_name;
        // echo "<br> output_operator---->";
        // print_r($request->output_operator);
        // echo "<br> gateway------>";
        // print_r($request->gateway);
        // echo "<br> associateSender------>";
        // print_r($request->associate_sender);
        // echo "<br> username------->";
        // print_r($request->username);
        // echo "<br> password------>";
        // print_r($request->password);
        // die();


        //create the master senderid
        $masterSenderId = SenderidMaster::create([
            'name'              => $request->sender_name,
            'type'              => $request->type,
            'description'       => $request->description,
            'created_by'        => Auth::guard('root')->user()->id,
            'active'            => 1,
        ]);


        $gsmOperators = Operators::where('type', 'gsm')->where('active', 1)->get();

        foreach ($gsmOperators as $operator) {
           $operatorId = $operator->id;

           $senderidGateway = SenderidGateways::create([
                'master_senderid'   => $masterSenderId->id,
                'input_operator'    => $operatorId,
                'output_operator'   => $output_operator[$operatorId],
                'senderid'          => $associate_sender[$operatorId],
                'gateway'           => $gateway[$operatorId],
                'username'          => $username[$operatorId],
                'password'          => $password[$operatorId],
                'created_by'        => Auth::guard('root')->user()->id,
                'active'            => 1,
           ]);

        }

        return back()->with(['msg' => 'Sender ID created successfully']);
    }


    public function showSenderIds(Request $request){        

        return view('smsview.senderid.manageSenderid');
    }

    public function getSenderIds(Request $request)
    {

        // storing  request (ie, get/post) global array to a variable  
        $requestData= $_REQUEST;

        // echo $requestData['start']."<br>";
        // echo $requestData['length']; die();


        $columns = array( 
        // datatable column index  => database column name
            0 =>'name', 
            1 => 'type',
            2 => 'description',
            3 => 'active',
            4 => 'creator',
            5 => 'action',
        );

        //laravel implementation
        $departments = Auth::user()->departments;
        
        $sql = "SELECT senderid_master.id, senderid_master.name, senderid_master.type, senderid_master.description, senderid_master.active, (SELECT root_users.name FROM root_users WHERE root_users.id=senderid_master.created_by) creator FROM senderid_master";


        $sql2 = "SELECT * FROM (". $sql .") result WHERE 1 ";

        $users = DB::select($sql2);

        $totalData = count($users);
        $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


        // getting records as per search parameters

        if( !empty($requestData['columns'][0]['search']['value']) ){  //code
            $sql2.=" AND name LIKE '%".$requestData['columns'][0]['search']['value']."%' ";
        }

        $rows = DB::select($sql2);
        
        $totalFiltered = count($rows); // when there is a search parameter then we have to modify total number filtered rows as per search result.
            
        $sql2.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length



        $rows = DB::select($sql2);


        $data = array();
        foreach ($rows as $row) {
            $nestedData=array(); 

            $nestedData[] = $row->name;

            if ($row->type==1) {

                $nestedData[] = 'Mask';
            } else if ($row->type==2) {

                $nestedData[] = 'Nonmask';
            } else if ($row->type==3) {

                $nestedData[] = 'Voice';
            }

            $nestedData[] = $row->description;

            if ($row->active==0) {

                $nestedData[] = 'Disabled';

            } else {

                $nestedData[] = 'Active';
            }

            $nestedData[] = $row->creator;

            $nestedData[] = '<a href="'.route('edit-senderid',$row->id).'" class="btn btn-sm btn-icon btn-pure btn-default senderidedtfrm"><i class="icon icon-pencil" aria-hidden="true"></i></a>

                <a href="#" class="btn btn-sm btn-icon btn-pure btn-default assignsenderid"  data-toggle="modal" data-target="#assignsenderid" data-original-title="Assign to Clients" data-id="'.$row->id.'" data-sendername="'.$row->name.'" title="Assign to Clients"><i class="fa fa-users" aria-hidden="true"></i></a>

                <a href="#" class="btn btn-sm btn-icon btn-pure btn-default assignsenderidReseller"  data-toggle="modal" data-target="#assignsenderidReseller" data-original-title="Assign to Resellers" data-id="'.$row->id.'" data-sendername="'.$row->name.'"><i class="fa fa-exchange" aria-hidden="true" title="Assign to Resellers"></i></a>';
            

            $data[] = $nestedData;
        }



        $json_data = array(
                    "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
                    "recordsTotal"    => intval( $totalData ),  // total number of records
                    "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
                    "data"            => $data   // total data array
                    );

        echo json_encode($json_data);  // send data as json format

    }


    public function loadAssignedSenderId(Request $request)
    {

        $senderid = $request->id;

        // $activeclients = $this->client
        //                     ->activeClients();

        $clients = User::whereNotIn('id', function($query) use($senderid){
                        $query->select('user')
                        ->from('senderid_users')
                        ->where('senderid', $senderid)
                        ->get();
                    })->get();


        $assignedClients = SenderidUsers::where('senderid', $senderid)->get();


        $smssenderidinfo = SenderidMaster::find($senderid);

        return view('smsview.senderid.load-assigned-senderid',compact(
                                                                        'clients',
                                                                        'assignedClients',
                                                                        'smssenderidinfo'))
                                                                ->with('senderid',$senderid);
    }

    public function loadAssignedSenderIdReseller(Request $request)
    {

        $senderid = $request->id;

        // $activeclients = $this->client
        //                     ->activeClients();

        $resellers = Reseller::whereNotIn('id', function($query) use($senderid){
                        $query->select('reseller')
                        ->from('senderid_resellers')
                        ->where('senderid', $senderid)
                        ->get();
                    })->get();


        $assignedResellers = SenderidResellers::where('senderid', $senderid)->get();


        $senderidInfo = SenderidMaster::find($senderid);

        return view('smsview.senderid.load-assigned-senderid-resellers',
            compact(                                                                           'resellers',
                    'assignedResellers',
                    'senderidInfo'))
            ->with('senderid',$senderid);
    }

    public function assignSenderIdToClient(Request $request)
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

            $clients = $request->activeclients;
            $senderid = $request->sender_id;

            foreach($clients as $user) {
                $senderidUser = SenderidUsers::create([
                    'senderid'      => $senderid,
                    'user'          => $user,
                    'user_type'     => $usertype,
                    'created_by'    => $owner->id,
                    'updated_by'    => $owner->id,
                    'active'        => 1,
                ]);


                if (Auth::guard('manager')->check()) {

                    $manager = Auth::guard('manager')->user()->name;

                    DB::table("staff_activities")
                        ->insert([
                            'manager_id' => Auth::guard('manager')->user()->id,
                            'activity_name' => 'Assign Sender ID',
                            'activity_type' => 'Insert',
                            'activity_desc' => "Manager {$manager} assign senderid to {$senderidUser->getUserOfSenderid->name}",
                            'record_id' => $senderidUser->id,
                            'invoice_val' => 0,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                }
                
            }
            
        }

        return back()->with('msg','Senderid successfully assigned'); 
    }

    public function assignSenderIdToReseller(Request $request)
    {
        if (is_array($request->activeResellers)) {

            if (Auth::guard('root')->check())
            {
                $owner = Auth::guard('root')->user();
                $usertype = 'root';
                
            } else {
                $owner = Auth::guard('manager')->user();
                $usertype = 'manager';
                
            }

            $resellers = $request->activeResellers;
            $senderid = $request->sender_id;

            foreach($resellers as $reseller) {
                $senderidReseller = SenderidResellers::create([
                    'senderid'      => $senderid,
                    'reseller'      => $reseller,
                    'user_type'     => $usertype,
                    'created_by'    => $owner->id,
                    'updated_by'    => $owner->id,
                    'active'        => 1,
                ]);


                if (Auth::guard('manager')->check()) {

                    $manager = Auth::guard('manager')->user()->name;

                    DB::table("staff_activities")
                        ->insert([
                            'manager_id' => Auth::guard('manager')->user()->id,
                            'activity_name' => 'Assign Sender ID',
                            'activity_type' => 'Insert',
                            'activity_desc' => "Manager {$manager} assign senderid to {$senderidReseller->getResellerOfSenderid->name}",
                            'record_id' => $senderidReseller->id,
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
     * Delete Assigned senderid id of a client
     *
     * @param int $assigned_user_senderid
     * @param int $senderid
     * @return void
     */
    public function deleteClientAssignedSenderId($senderid_users_id, $senderid)
    {
        $senderid_userData = SenderidUsers::where('senderid', $senderid)
        ->where('user',$senderid_users_id)
        ->first();

        if ($senderid_userData)
        {            
            $senderid_userData->delete();

            return response()->json(['msg' => 'Senderid deleted successfully'],200);
        }

        return response()->json(['errmsg' => 'Senderid not found or invalid request'], 406);


    }

    /**
     * Delete Assigned senderid id of a reseller
     *
     * @param int $assigned_user_senderid
     * @param int $senderid
     * @return void
     */
    public function deleteResellerAssignedSenderId($reseller, $senderid)
    {

        if (!Auth::guard('root')->check() && !Auth::guard('manager')->user())
        {
            return response()->json(['errmsg' => 'Invalid request'], 406);
            
        }

        $senderidResellerData = SenderidResellers::where('senderid', $senderid)
        ->where('reseller',$reseller)
        ->first();

        if ($senderidResellerData)
        {            
            $senderidResellerData->delete();

            return response()->json(['msg' => 'Senderid deleted successfully'],200);
        }

        return response()->json(['errmsg' => 'Senderid not found or invalid request'], 406);


    }

    //edit senderid
    public function editSenderIds($senderid)
    {
        $senderId = SenderidMaster::find($senderid);
        if (!$senderId) {
            return back()->with(['errmsg' => 'Invalid Sender ID']);
        }

        $gsmOperators = Operators::where('type', 'gsm')->where('active', 1)->get();
        $allOperators = Operators::where('active', 1)->get();
        $gateways = OperatorGateways::get();
        $senderidGateways = SenderidGateways::where('master_senderid', $senderid)->get();

        $oldOutputOperators = [];
        $oldGatewayId = [];
        $oldGatewayOptions = [];
        $oldAssociate = [];
        $oldUsername= [];
        $oldPass= [];


        foreach ($senderidGateways as $gateWay) {

            $oldOutputOperators[$gateWay->input_operator]   = $gateWay->output_operator;
            $oldGatewayId[$gateWay->input_operator]           = $gateWay->gateway;
            $oldAssociate[$gateWay->input_operator]         = $gateWay->senderid;
            $oldUsername[$gateWay->input_operator]          = $gateWay->username;
            $oldPass[$gateWay->input_operator]              = $gateWay->password;

            //old gateway options
            $gatewayOfOutputOperator = OperatorGateways::where('operator_id', $gateWay->output_operator)->get();

            $oldGatewayOption = '';
            foreach ($gatewayOfOutputOperator as $outputGateway) {

                $selected = $outputGateway->id==$gateWay->gateway ? 'selected' : '';

                $oldGatewayOption .= '<option value="'.$outputGateway->id.'"  '.$selected .'>'.$outputGateway->name.'</option>';
            }

            $oldGatewayOptions[$gateWay->input_operator]    = $oldGatewayOption;
        
        }

        return view('smsview.senderid.editSenderid',compact('senderId', 'senderidGateways', 'gateways','gsmOperators', 'allOperators', 'oldOutputOperators', 'oldGatewayId', 'oldAssociate', 'oldUsername', 'oldPass', 'oldGatewayOptions'));
    }

    public function editSenderidPost($senderid, Request $request)
    {

        $masterSenderId = SenderidMaster::find($senderid);


        if (!$masterSenderId) {
            return back()->with(['errmsg' => 'Sender ID not found']);
        }

        $validatedAttributes = $this->validateSenderId();

        $output_operator    = $request->output_operator;
        $gateway            = $request->gateway;
        $associate_sender   = $request->associate_sender;
        $username           = $request->username;
        $password           = $request->password;


        //create the master senderid
        $masterSenderId->name         = $request->sender_name;
        $masterSenderId->type         = $request->type;
        $masterSenderId->description  = $request->description;
        $masterSenderId->updated_by   = Auth::guard('root')->user()->id;
        $masterSenderId->active       = $request->status;
        $masterSenderId->save();


        $gsmOperators = Operators::where('type', 'gsm')->where('active', 1)->get();

        foreach ($gsmOperators as $operator) {
           
           $operatorId = $operator->id;

           $senderidGateway = SenderidGateways::where('master_senderid', $senderid)->where('input_operator', $operatorId)->first();

           $senderidGateway->output_operator    = $output_operator[$operatorId];
           

           if ($output_operator[$operatorId]!=5) {

                $senderidGateway->username            = '';
                $senderidGateway->password           = '';
                $senderidGateway->senderid           = $associate_sender[$operatorId];
           } else {
                $senderidGateway->username            = $username[$operatorId];
                $senderidGateway->password           = $password[$operatorId];
                $senderidGateway->senderid           = '';
           }

           $senderidGateway->gateway            = $gateway[$operatorId];
           $senderidGateway->updated_by         = Auth::guard('root')->user()->id;
           $senderidGateway->save();

        }

        return back()->with(['msg' => 'Sender ID Updated Successfully']);
    }


    public function resellerSenderidList(Request $request)
    {
        $resellerid = !empty($request->resellerid) ? $request->resellerid : Auth::guard('reseller')->user()->id;

        $senderids = SenderidResellers::where('reseller', $resellerid)->get();

        $resellerSenderid = [];

        foreach($senderids as $senderid)
        {
            $resellerSenderid[] = $senderid->getSenderid->name;
        }

        return $resellerSenderid;
        
    }

    public function clientSenderidList(Request $request)
    {
        $clientid = !empty($request->clientid) ? $request->clientid : Auth::guard('web')->user()->id;

        $senderids = SenderidUsers::where('user', $clientid)->get();

        $clientSenderid = [];

        foreach($senderids as $senderid)
        {
            $clientSenderid[] = $senderid->getSenderid->name;
        }

        return $clientSenderid;
        
    }





    protected function validateSenderId()
    {
        return request()->validate([
            'sender_name'   => ['required','max:13'],
            'type'          => ['required'],
            'description'   => ['max:255'],
        ]);
    }
    



}
