<?php

namespace App\Http\Controllers;

//use DB;
use App\User;

use Carbon\Carbon;
use App\SmsCampaigns;
use Illuminate\Support\Arr;
use App\SmsCampaignNumbersA;
use App\SmsCampaignNumbersB;
use App\SmsCampaignNumbersC;


use App\SmsCampaignNumbersD;
use App\SmsCampaignNumbersE;
use Illuminate\Http\Request;
use App\Core\Reports\SmsReport;
use App\Datatables\DataTableClass;
use Illuminate\Support\Facades\DB;
use App\Core\Users\ClientInterface;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class SmsReportController extends Controller
{
    /**
     * Client service
     *
     * @var App\Core\Users\ClientRepository
     */
    protected $client;

    protected $smsreport;

    public function __construct(
        ClientInterface $client,
        SmsReport $smsreport
    )
    {
        $this->middleware('auth:web,root,reseller,manager');

        $this->client = $client;
        $this->smsreport = $smsreport;
        ini_set('max_execution_time', 0);
    }

    public function clientSingleSmsReport()
    {
        return view('smsview.smsreport.clientSingleSmsReport');
    }

    public function clientCampaignReport()
    {
        return view('smsview.smsreport.clientCampaignReport');
    }


    public function clientSingleSmsReportData(Request $request)
    {

        // storing  request (ie, get/post) global array to a variable  
        $requestData= $_REQUEST;

        // echo $requestData['start']."<br>";
        // echo $requestData['length']; die();


        $columns = array( 
        // datatable column index  => database column name
            0 => 'sms_id', 
            1 => 'master_senderid',
            2 => 'category',
            3 => 'number',
            4 => 'sent_through',
            5 => 'qty',
            6 => 'created_at',
            7 => 'status',
            8 => 'content',
        );

        //laravel implementation
        $userId = Auth::guard('web')->user()->id;
        
        $sql = "SELECT sms_individuals.sms_id, (SELECT senderid_master.name FROM senderid_master WHERE senderid_master.id=sms_individuals.sender_id) master_senderid, sms_individuals.category, sms_individuals.number, sms_individuals.sent_through, sms_individuals.qty, sms_individuals.created_at, sms_individuals.status, sms_individuals.content FROM sms_individuals WHERE user_id=$userId ";


        $sql2 = "SELECT * FROM (". $sql .") result WHERE 1 ";

        $users = DB::select($sql2);

        $totalData = count($users);
        $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


        // getting records as per search parameters

        if( !empty($requestData['columns'][0]['search']['value']) ){  //code
            $sql2.=" AND sms_id LIKE '%".$requestData['columns'][0]['search']['value']."%' ";
        }
        if( !empty($requestData['columns'][1]['search']['value']) ){  //code
            $sql2.=" AND master_senderid LIKE '%".$requestData['columns'][1]['search']['value']."%' ";
        }
        if( !empty($requestData['columns'][3]['search']['value']) ){  //code
            $sql2.=" AND number LIKE '%".$requestData['columns'][3]['search']['value']."%' ";
        }if( !empty($requestData['columns'][6]['search']['value']) ){  //code
            $sql2.=" AND created_at LIKE '%".$requestData['columns'][6]['search']['value']."%' ";
        }

        $rows = DB::select($sql2);
        
        $totalFiltered = count($rows); // when there is a search parameter then we have to modify total number filtered rows as per search result.
            
        $sql2.=" ORDER BY  created_at desc   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length



        $rows = DB::select($sql2);


        $data = array();
        foreach ($rows as $row) {
            $nestedData=array(); 

            $nestedData[] = $row->sms_id;
            $nestedData[] = $row->master_senderid;

            if ($row->category==1) {

                $nestedData[] = 'Mask';
            } else if ($row->category==2) {

                $nestedData[] = 'Nonmask';
            } else if ($row->category==3) {

                $nestedData[] = 'Voice';
            }

            $nestedData[] = $row->number;
            
            if ($row->sent_through==0) {
                $nestedData[] = "Web";
            } else {
                $nestedData[] = "API";
            }

            $nestedData[] = $row->qty;
            $nestedData[] = $row->created_at;

            if ($row->status==0) {
                $nestedData[] = "Pending";
            } else if ($row->status==1) {
                $nestedData[] = "Sent";
            } else if ($row->status==2) {
                $nestedData[] = "Delivered";
            } else if ($row->status==3) {
                $nestedData[] = "Failed";
            } else if ($row->status==4) {
                $nestedData[] = "UnDelivered";
            } else if ($row->status==5) {
                $nestedData[] = "Transmitted";
            }
            
            
            $nestedData[] = $row->content;

            

            // $nestedData[] = '<a href="'.route('edit-senderid',$row->id).'" class="btn btn-sm btn-icon btn-pure btn-default senderidedtfrm"><i class="icon icon-pencil" aria-hidden="true"></i></a>

            //     <a href="#" class="btn btn-sm btn-icon btn-pure btn-default assignsenderid"  data-toggle="modal" data-target="#assignsenderid" data-original-title="edit" data-id="'.$row->id.'" data-sendername="'.$row->name.'"><i class="fa fa-users" aria-hidden="true"></i></a>';
            

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


    public function rootSingleSmsReportData(Request $request)
    {
        // storing  request (ie, get/post) global array to a variable  
        $requestData= $_REQUEST;

        $columns = array( 
        // datatable column index  => database column name
            0 => 'sms_id', 
            1 => 'client',
            2 => 'master_senderid',
            3 => 'content',
            4 => 'number',
            5 => 'created_at',
            6 => 'status',
        );

        //laravel implementation
        $sql = "SELECT sms_individuals.sms_id, (SELECT users.name FROM users WHERE sms_individuals.user_id=users.id) client, (SELECT senderid_master.name FROM senderid_master WHERE senderid_master.id=sms_individuals.sender_id) master_senderid, sms_individuals.number, sms_individuals.created_at, sms_individuals.status, sms_individuals.content FROM sms_individuals WHERE 1=1 ";

        $sql2 = "SELECT * FROM (". $sql .") result WHERE 1 ";
        $sql3 = "SELECT count(*) as cnt FROM sms_individuals WHERE 1 ";

        $smsCnt = DB::select($sql3);

        $totalData = $smsCnt[0]->cnt;

        $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


        // getting records as per search parameters

        if( !empty($requestData['columns'][1]['search']['value']) ){  //code
            $sql2.=" AND client LIKE '%".$requestData['columns'][1]['search']['value']."%' ";
        }
        if( !empty($requestData['columns'][2]['search']['value']) ){  //code
            $sql2.=" AND master_senderid LIKE '%".$requestData['columns'][2]['search']['value']."%' ";
        }
        if( !empty($requestData['columns'][3]['search']['value']) ){  //code
            $sql2.=" AND content LIKE '%".$requestData['columns'][3]['search']['value']."%' ";
        }
        if( !empty($requestData['columns'][4]['search']['value']) ){  //code
            $sql2.=" AND content LIKE '%".$requestData['columns'][4]['search']['value']."%' ";
        }

        $sql4 = $sql2.' LIMIT 10000 ';

        $rows = DB::select($sql4);
        
        $totalFiltered = count($rows); // when there is a search parameter then we have to modify total number filtered rows as per search result.
            
        $sql2.=" ORDER BY  created_at desc   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

        $rows = DB::select($sql2);


        $data = array();
        foreach ($rows as $row) {
            $nestedData=array();

            $nestedData[] = $row->sms_id;
            $nestedData[] = $row->client;
            $nestedData[] = $row->master_senderid;
            $nestedData[] = $row->content;
            $nestedData[] = $row->number;
            $nestedData[] = $row->created_at;

            if ($row->status==0) {
                $nestedData[] = "Pending";
            } else if ($row->status==1) {
                $nestedData[] = "Sent";
            } else if ($row->status==2) {
                $nestedData[] = "Delivered";
            } else if ($row->status==3) {
                $nestedData[] = "Failed";
            } else if ($row->status==4) {
                $nestedData[] = "UnDelivered";
            } else if ($row->status==5) {
                $nestedData[] = "Transmitted";
            }
            

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

    public function clientCampaignReportData(Request $request)
    {

        // storing  request (ie, get/post) global array to a variable  
        $requestData= $_REQUEST;

        // echo $requestData['start']."<br>";
        // echo $requestData['length']; die();


        
        // var_dump(Auth::guard('web')->user()); exit;
        // var_dump(Auth::guard('root')->user()); exit;

        //laravel implementation
        $userQueryPart = "";
        $rootQueryPart = "";
        $searchIndex = 0;
        if(Auth::guard('web')->user()){
            $userId = Auth::guard('web')->user()->id;
            $userQueryPart = "WHERE user_id=".$userId;
            
            $columns = array( 
            // datatable column index  => database column name
                0 => 'campaign_no',
                1 => 'campaign_name', 
                2 => 'master_senderid',
                3 => 'category',
                4 => 'sent_through',
                5 => 'total_numbers',
                6 => 'sms_qty',
                7 => 'created_at',
                8 => 'status',
            );
        } else {
            $rootQueryPart = " (SELECT users.name FROM users WHERE users.id=sms_campaigns.user_id) client_name, ";
            $searchIndex = 1;
            
            $columns = array( 
            // datatable column index  => database column name
                0 => 'client_name',
                1 => 'campaign_no',
                2 => 'campaign_name', 
                3 => 'master_senderid',
                4 => 'category',
                5 => 'sent_through',
                6 => 'total_numbers',
                7 => 'sms_qty',
                8 => 'created_at',
                9 => 'status',
            );
        }
        
        
        
        
        
        $sql = "SELECT sms_campaigns.id, sms_campaigns.campaign_no, ".$rootQueryPart." sms_campaigns.campaign_name, (SELECT senderid_master.name FROM senderid_master WHERE senderid_master.id=sms_campaigns.sender_id) master_senderid, sms_campaigns.category,sms_campaigns.sent_through, sms_campaigns.total_numbers, sms_campaigns.sms_qty, sms_campaigns.created_at, sms_campaigns.status FROM sms_campaigns ".$userQueryPart;


        $sql2 = "SELECT * FROM (". $sql .") result WHERE 1 ";

        $users = DB::select($sql2);

        $totalData = count($users);
        $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


        // getting records as per search parameters
        if(!Auth::guard('web')->user()){

            if( !empty($requestData['columns'][0]['search']['value']) ){  //code
                $sql2.=" AND client_name LIKE '%".$requestData['columns'][0]['search']['value']."%' ";
            }
        }

        if( !empty($requestData['columns'][$searchIndex+0]['search']['value']) ){  //code
            $sql2.=" AND campaign_no LIKE '%".$requestData['columns'][$searchIndex+0]['search']['value']."%' ";
        }
        if( !empty($requestData['columns'][$searchIndex+1]['search']['value']) ){  //code
            $sql2.=" AND campaign_name LIKE '%".$requestData['columns'][$searchIndex+1]['search']['value']."%' ";
        }
        if( !empty($requestData['columns'][$searchIndex+2]['search']['value']) ){  //code
            $sql2.=" AND master_senderid LIKE '%".$requestData['columns'][$searchIndex+2]['search']['value']."%' ";
        }
        if( !empty($requestData['columns'][$searchIndex+7]['search']['value']) ){  //code
            $sql2.=" AND created_at LIKE '%".$requestData['columns'][$searchIndex+7]['search']['value']."%' ";
        }
        
        

        $rows = DB::select($sql2);
        // echo $columns[$requestData['order'][0]['column']]; exit;
        
        $totalFiltered = count($rows); // when there is a search parameter then we have to modify total number filtered rows as per search result.
            
        $sql2.=" ORDER BY  created_at desc   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length



        $rows = DB::select($sql2);


        $data = array();
        foreach ($rows as $row) {
            $nestedData=array(); 
            
            if(!Auth::guard('web')->user()){
                $nestedData[] = $row->client_name;
            }

            $nestedData[] = $row->campaign_no;
            $nestedData[] = $row->campaign_name;
            $nestedData[] = $row->master_senderid;

            if ($row->category==1) {

                $nestedData[] = 'Mask';
            } else if ($row->category==2) {

                $nestedData[] = 'Nonmask';
            } else if ($row->category==3) {

                $nestedData[] = 'Voice';
            }
            
            if ($row->sent_through==0) {
                $nestedData[] = "Web";
            } else {
                $nestedData[] = "API";
            }

            $nestedData[] = $row->total_numbers;
            $nestedData[] = $row->sms_qty;
            $nestedData[] = $row->created_at;
            $nestedData[] = $row->status;            

            if ($row->status=='Complete') {
                $nestedData[] = '<a href="'.route('campaign-report-details',$row->id).'" class="btn btn-sm btn-icon btn-pure btn-default" title="Details Report"><i class="fa fa-book" aria-hidden="true"></i></a>';
            } else {
                $nestedData[] = '<a href="'.route('campaign-report-details',$row->id).'" class="btn btn-sm btn-icon btn-pure btn-default" title="Details Report"><i class="fa fa-book" aria-hidden="true"></i></a>  <a href="'.route('campaign-details-live',$row->id).'" class="btn btn-sm btn-icon btn-pure btn-default" title="Real-time Report"><i class="fa fa-clock-o" aria-hidden="true"></i></a>';
            }

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


    public function clientCampaignReportDetails($campaignId, Request $request)
    {

        $campaign = SmsCampaigns::find($campaignId);

        if (!$campaign) {
            return redirect()->route('campaign-report')->with(['errmsg' => 'Invalid Request!']);
        }
        
        if(Auth::guard('web')->user()){
            if ($campaign->user_id!=Auth::guard('web')->user()->id) {
                return redirect()->route('campaign-report')->with(['errmsg' => 'Invalid Request!']);
            }
        }
        
        $api_token = User::find($campaign->user_id)->api_token;

        if ($campaign->category==1) {

            $type = 'Mask';
        } else if ($campaign->category==2) {

            $type = 'Nonmask';
        } else if ($campaign->category==3) {

            $type = 'Voice';
        }

        if ($campaign->sent_through==0) {
            $sent_through = "Web";
        } else {
            $sent_through = "API";
        }

        if ($campaign->is_unicode==1) {
            $smsType = "Unicode";
        } else {
            $smsType = "Text";
        }

        $campaignType = $campaign->campaign_type;
        if ($campaignType=='A') {
            
            $totalNumbers['GP']     = SmsCampaignNumbersA::where('campaign_id', $campaignId)->where('operator', 1)->count();
            $totalNumbers['BL']     = SmsCampaignNumbersA::where('campaign_id', $campaignId)->where('operator', 2)->count();
            $totalNumbers['Airtel'] = SmsCampaignNumbersA::where('campaign_id', $campaignId)->where('operator', 3)->count();
            $totalNumbers['Robi']   = SmsCampaignNumbersA::where('campaign_id', $campaignId)->where('operator', 4)->count();
            $totalNumbers['Ttk']    = SmsCampaignNumbersA::where('campaign_id', $campaignId)->where('operator', 5)->count();

        } else if ($campaignType=='B') {
            
            $totalNumbers['GP']     = SmsCampaignNumbersB::where('campaign_id', $campaignId)->where('operator', 1)->count();
            $totalNumbers['BL']     = SmsCampaignNumbersB::where('campaign_id', $campaignId)->where('operator', 2)->count();
            $totalNumbers['Airtel'] = SmsCampaignNumbersB::where('campaign_id', $campaignId)->where('operator', 3)->count();
            $totalNumbers['Robi']   = SmsCampaignNumbersB::where('campaign_id', $campaignId)->where('operator', 4)->count();
            $totalNumbers['Ttk']    = SmsCampaignNumbersB::where('campaign_id', $campaignId)->where('operator', 5)->count();

        } else if ($campaignType=='C') {

            $totalNumbers['GP']     = SmsCampaignNumbersC::where('campaign_id', $campaignId)->where('operator', 1)->count();
            $totalNumbers['BL']     = SmsCampaignNumbersC::where('campaign_id', $campaignId)->where('operator', 2)->count();
            $totalNumbers['Airtel'] = SmsCampaignNumbersC::where('campaign_id', $campaignId)->where('operator', 3)->count();
            $totalNumbers['Robi']   = SmsCampaignNumbersC::where('campaign_id', $campaignId)->where('operator', 4)->count();
            $totalNumbers['Ttk']    = SmsCampaignNumbersC::where('campaign_id', $campaignId)->where('operator', 5)->count();

        } else if ($campaignType=='D') {
            
            $totalNumbers['GP']     = SmsCampaignNumbersD::where('campaign_id', $campaignId)->where('operator', 1)->count();
            $totalNumbers['BL']     = SmsCampaignNumbersD::where('campaign_id', $campaignId)->where('operator', 2)->count();
            $totalNumbers['Airtel'] = SmsCampaignNumbersD::where('campaign_id', $campaignId)->where('operator', 3)->count();
            $totalNumbers['Robi']   = SmsCampaignNumbersD::where('campaign_id', $campaignId)->where('operator', 4)->count();
            $totalNumbers['Ttk']    = SmsCampaignNumbersD::where('campaign_id', $campaignId)->where('operator', 5)->count();

        } else if ($campaignType=='E') {
            
            $totalNumbers['GP']     = SmsCampaignNumbersE::where('campaign_id', $campaignId)->where('operator', 1)->count();
            $totalNumbers['BL']     = SmsCampaignNumbersE::where('campaign_id', $campaignId)->where('operator', 2)->count();
            $totalNumbers['Airtel'] = SmsCampaignNumbersE::where('campaign_id', $campaignId)->where('operator', 3)->count();
            $totalNumbers['Robi']   = SmsCampaignNumbersE::where('campaign_id', $campaignId)->where('operator', 4)->count();
            $totalNumbers['Ttk']    = SmsCampaignNumbersE::where('campaign_id', $campaignId)->where('operator', 5)->count();

        }
        
        //get the user info
        $campaignUser = User::find($campaign->user_id);
        return view('smsview.smsreport.clientCampaignDetails', compact('campaign', 'type', 'sent_through', 'smsType', 'totalNumbers', 'api_token', 'campaignUser'));
    }


    public function campaignDetailsData($campaignId, Request $request)
    {

        $campaign = SmsCampaigns::find($campaignId);

        if (!$campaign) {
            return response()->json(['errmsg' => 'Invalid Request'], 406);
        }

        $campaignType = $campaign->campaign_type;
        if ($campaignType=='A') {
            $campaignNumbersTable = 'sms_campaign_numbers';
        } else if ($campaignType=='B') {
            $campaignNumbersTable = 'sms_campaign_numbersB';
        } else if ($campaignType=='C') {
            $campaignNumbersTable = 'sms_campaign_numbersC';
        } else if ($campaignType=='D') {
            $campaignNumbersTable = 'sms_campaign_numbersD';
        } else if ($campaignType=='E') {
            $campaignNumbersTable = 'sms_campaign_numbersE';
        }

        // storing  request (ie, get/post) global array to a variable  
        $requestData= $_REQUEST;

        // echo $requestData['start']."<br>";
        // echo $requestData['length']; die();


        $columns = array( 
        // datatable column index  => database column name
            0 => 'number', 
            1 => 'operator_name', 
            2 => 'status',
        );

        //laravel implementation
        $userId = $campaign->user_id;
        
        $sql = "SELECT $campaignNumbersTable.id, $campaignNumbersTable.number,
        $campaignNumbersTable.status, (SELECT operators.name FROM operators WHERE operators.id=$campaignNumbersTable.operator) operator_name FROM $campaignNumbersTable WHERE campaign_id=$campaignId ";


        $sql2 = "SELECT * FROM (". $sql .") result WHERE 1 ";

        $users = DB::select($sql2);

        $totalData = count($users);
        $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


        // getting records as per search parameters

        if( !empty($requestData['columns'][0]['search']['value']) ){  //code
            $sql2.=" AND number LIKE '%".$requestData['columns'][0]['search']['value']."%' ";
        }

        $rows = DB::select($sql2);
        
        $totalFiltered = count($rows); // when there is a search parameter then we have to modify total number filtered rows as per search result.
            
        $sql2.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length



        $rows = DB::select($sql2);


        $data = array();
        foreach ($rows as $row) {
            $nestedData=array(); 

            $nestedData[] = $row->number;
            $nestedData[] = $row->operator_name;

            if ($row->status==0) {
                $nestedData[] = "Pending";
            } else if ($row->status==1) {
                $nestedData[] = "Sent";
            } else if ($row->status==2) {
                $nestedData[] = "Delivered";
            } else if ($row->status==3) {
                $nestedData[] = "Failed";
            } else if ($row->status==4) {
                $nestedData[] = "UnDelivered";
            } else if ($row->status==5) {
                $nestedData[] = "Transmitted";
            }

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


    public function campaignDetailsLive($campaignId, Request $request)
    {

        $campaign = SmsCampaigns::find($campaignId);

        if (!$campaign) {
            return redirect()->route('campaign-report')->with(['errmsg' => 'Invalid Request!']);
        }
        
        if(Auth::guard('web')->user()){
            if ($campaign->user_id!=Auth::guard('web')->user()->id) {
                return redirect()->route('campaign-report')->with(['errmsg' => 'Invalid Request!']);
            }
        }
        
        $api_token = User::find($campaign->user_id)->api_token;
        

        if ($campaign->category==1) {
            $type = 'Mask';
        } else if ($campaign->category==2) {
            $type = 'Nonmask';
        } else if ($campaign->category==3) {
            $type = 'Voice';
        }

        if ($campaign->sent_through==0) {
            $sent_through = "Web";
        } else {
            $sent_through = "API";
        }

        if ($campaign->is_unicode==1) {
            $smsType = "Unicode";
        } else {
            $smsType = "Text";
        }

        $campaignType = $campaign->campaign_type;
        if ($campaignType=='A') {
            
            $totalNumbers['GP']     = SmsCampaignNumbersA::where('campaign_id', $campaignId)->where('operator', 1)->count();
            $totalNumbers['BL']     = SmsCampaignNumbersA::where('campaign_id', $campaignId)->where('operator', 2)->count();
            $totalNumbers['Airtel'] = SmsCampaignNumbersA::where('campaign_id', $campaignId)->where('operator', 3)->count();
            $totalNumbers['Robi']   = SmsCampaignNumbersA::where('campaign_id', $campaignId)->where('operator', 4)->count();
            $totalNumbers['Ttk']    = SmsCampaignNumbersA::where('campaign_id', $campaignId)->where('operator', 5)->count();

        } else if ($campaignType=='B') {
            
            $totalNumbers['GP']     = SmsCampaignNumbersB::where('campaign_id', $campaignId)->where('operator', 1)->count();
            $totalNumbers['BL']     = SmsCampaignNumbersB::where('campaign_id', $campaignId)->where('operator', 2)->count();
            $totalNumbers['Airtel'] = SmsCampaignNumbersB::where('campaign_id', $campaignId)->where('operator', 3)->count();
            $totalNumbers['Robi']   = SmsCampaignNumbersB::where('campaign_id', $campaignId)->where('operator', 4)->count();
            $totalNumbers['Ttk']    = SmsCampaignNumbersB::where('campaign_id', $campaignId)->where('operator', 5)->count();

        } else if ($campaignType=='C') {

            $totalNumbers['GP']     = SmsCampaignNumbersC::where('campaign_id', $campaignId)->where('operator', 1)->count();
            $totalNumbers['BL']     = SmsCampaignNumbersC::where('campaign_id', $campaignId)->where('operator', 2)->count();
            $totalNumbers['Airtel'] = SmsCampaignNumbersC::where('campaign_id', $campaignId)->where('operator', 3)->count();
            $totalNumbers['Robi']   = SmsCampaignNumbersC::where('campaign_id', $campaignId)->where('operator', 4)->count();
            $totalNumbers['Ttk']    = SmsCampaignNumbersC::where('campaign_id', $campaignId)->where('operator', 5)->count();

        } else if ($campaignType=='D') {
            
            $totalNumbers['GP']     = SmsCampaignNumbersD::where('campaign_id', $campaignId)->where('operator', 1)->count();
            $totalNumbers['BL']     = SmsCampaignNumbersD::where('campaign_id', $campaignId)->where('operator', 2)->count();
            $totalNumbers['Airtel'] = SmsCampaignNumbersD::where('campaign_id', $campaignId)->where('operator', 3)->count();
            $totalNumbers['Robi']   = SmsCampaignNumbersD::where('campaign_id', $campaignId)->where('operator', 4)->count();
            $totalNumbers['Ttk']    = SmsCampaignNumbersD::where('campaign_id', $campaignId)->where('operator', 5)->count();

        } else if ($campaignType=='E') {
            
            $totalNumbers['GP']     = SmsCampaignNumbersE::where('campaign_id', $campaignId)->where('operator', 1)->count();
            $totalNumbers['BL']     = SmsCampaignNumbersE::where('campaign_id', $campaignId)->where('operator', 2)->count();
            $totalNumbers['Airtel'] = SmsCampaignNumbersE::where('campaign_id', $campaignId)->where('operator', 3)->count();
            $totalNumbers['Robi']   = SmsCampaignNumbersE::where('campaign_id', $campaignId)->where('operator', 4)->count();
            $totalNumbers['Ttk']    = SmsCampaignNumbersE::where('campaign_id', $campaignId)->where('operator', 5)->count();

        }
        
        //get the user info
        $campaignUser = User::find($campaign->user_id);
        return view('smsview.smsreport.campaignDetailsLive', compact('campaign', 'type', 'sent_through', 'smsType', 'totalNumbers', 'api_token', 'campaignUser'));
    }
    
    public function roottest(Request $request)
    {
        echo "here:";
        var_dump(Auth::guard('root')->user());
    }



    public function clientSmsSendReport(Request $request)
    {
        
        $userid = $request->user()->id;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        // DB table to use
        $table = 'detail_dlr';

        // Table's primary key
        $primaryKey = 'sentrecid';

      
        $where = " userid = '".$userid."' and DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";
        

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        
        $columns = array(
            array( 'db' => 'sentrecid', 'dt' => 0 ),
            array( 'db' => 'smsid',   'dt' => 1 ),
            array( 'db' => 'client_name', 'dt' => 2 ),
            array( 'db' => 'client_email',  'dt' => 3 ),
            array( 'db' => 'sender_name',   'dt' => 4 ),
            array( 'db' => 'contact',   'dt' => 5 ),
            array( 'db' => 'sms_type',   'dt' => 6 ),
            array( 'db' => 'sms_catagory',   'dt' => 7 ),
            array( 'db' => 'smscount',   'dt' => 8 ),
            array( 'db' => 'send_from',   'dt' => 9 ),
            array( 'db' => 'submitted_at',   'dt' => 10 ),
            array( 'db' => 'status',   'dt' => 11 ),
            array( 'db' => 'sms_content',   'dt' => 12 )
        );

        // SQL server connection information
        $sql_details = array(
            'user' => env('DB_USERNAME'),
            'pass' => env('DB_PASSWORD'),
            'db'   => env('DB_DATABASE'),
            'host' => env('DB_HOST')
        );


        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */

        

        echo json_encode(DataTableClass::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $where));

    }


    


    public function clientArchiveConsulateSmsSendReport(Request $request)
    {
        
        $userid = $request->user()->id;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        // DB table to use
        $table = 'archive_campaign';

        // Table's primary key
        $primaryKey = 'smsid';

      
        $where = " userid = '".$userid."' and DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";
        

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        
        $columns = array(
            array( 'db' => 'smsid',   'dt' => 0 ),
            array( 'db' => 'sender_name',   'dt' => 1 ),
            array( 'db' => 'sms_type',   'dt' => 2 ),
            array( 'db' => 'name',   'dt' => 3 ),
            array( 'db' => 'contact',   'dt' => 4 ),
            array( 'db' => 'smscount',   'dt' => 5 ),
            array( 'db' => 'send_from',   'dt' => 6 ),
            array( 'db' => 'submitted_at',   'dt' => 7 ),
            array( 'db' => 'status',   'dt' => 8 ),
            array( 'db' => 'sms_content',   'dt' => 9 ),
            array( 'db' => 'totalcampaign',   'dt' => 10 ),
        );

        // SQL server connection information
        $sql_details = array(
            'user' => env('DB_USERNAME'),
            'pass' => env('DB_PASSWORD'),
            'db'   => env('DB_DATABASE'),
            'host' => env('DB_HOST')
        );


        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */

        

        echo json_encode(DataTableClass::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $where));

    }


    public function rootClientConsulateSmsSendReport(Request $request)
    {
        
        $userid = $request->user()->id;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        // DB table to use
        $table = 'consulate_dlr';

        // Table's primary key
        $primaryKey = 'smsid';

      
        //$where = " DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";
        if (!empty($request->fromdate) && !empty($request->todate) && !empty($request->userid))
        {

            $where = " userid = '".$request->userid."' and DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";
        
        } else if(!empty($request->fromdate) && !empty($request->todate) && empty($request->userid)) {

            $where = " DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";

        }else {

            $where = " userid = '".$request->userid."'";

        }
        

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        
        $columns = array(
            array( 'db' => 'smsid',   'dt' => 0 ),
            array( 'db' => 'sender_name',   'dt' => 1 ),
            array( 'db' => 'sms_type',   'dt' => 2 ),
            array( 'db' => 'name',   'dt' => 3 ),
            array( 'db' => 'contact',   'dt' => 4 ),
            array( 'db' => 'smscount',   'dt' => 5 ),
            array( 'db' => 'send_from',   'dt' => 6 ),
            array( 'db' => 'submitted_at',   'dt' => 7 ),
            array( 'db' => 'status',   'dt' => 8 ),
            array( 'db' => 'sms_content',   'dt' => 9 ),
            array( 'db' => 'totalcampaign',   'dt' => 10 ),
        );

        // SQL server connection information
        $sql_details = array(
            'user' => env('DB_USERNAME'),
            'pass' => env('DB_PASSWORD'),
            'db'   => env('DB_DATABASE'),
            'host' => env('DB_HOST')
        );


        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */

        

        echo json_encode(DataTableClass::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $where));

    }

    public function rootClientArchiveConsulateSmsSendReport(Request $request)
    {
        
        $userid = $request->user()->id;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        // DB table to use
        $table = 'archive_campaign';

        // Table's primary key
        $primaryKey = 'smsid';

      
        //$where = " DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";
        if (!empty($request->fromdate) && !empty($request->todate) && !empty($request->userid))
        {

            $where = " userid = '".$request->userid."' and DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";
        
        } else if(!empty($request->fromdate) && !empty($request->todate) && empty($request->userid)) {

            $where = " DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";

        }else {

            $where = " userid = '".$request->userid."'";

        }
        

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        
        $columns = array(
            array( 'db' => 'smsid',   'dt' => 0 ),
            array( 'db' => 'sender_name',   'dt' => 1 ),
            array( 'db' => 'sms_type',   'dt' => 2 ),
            array( 'db' => 'name',   'dt' => 3 ),
            array( 'db' => 'contact',   'dt' => 4 ),
            array( 'db' => 'smscount',   'dt' => 5 ),
            array( 'db' => 'send_from',   'dt' => 6 ),
            array( 'db' => 'submitted_at',   'dt' => 7 ),
            array( 'db' => 'status',   'dt' => 8 ),
            array( 'db' => 'sms_content',   'dt' => 9 ),
            array( 'db' => 'totalcampaign',   'dt' => 10 ),
        );

        // SQL server connection information
        $sql_details = array(
            'user' => env('DB_USERNAME'),
            'pass' => env('DB_PASSWORD'),
            'db'   => env('DB_DATABASE'),
            'host' => env('DB_HOST')
        );


        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */

        

        echo json_encode(DataTableClass::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $where));

    }


    public function resellerClientConsulateSmsSendReport(Request $request)
    {
        
        $userid = $request->userid;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        $resellerid = Auth::guard('reseller')->user()->id;

        // DB table to use
        $table = 'consulate_dlr';

        // Table's primary key
        $primaryKey = 'smsid';

      
        //$where = " DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";
        if (!empty($request->fromdate) && !empty($request->todate) && !empty($request->userid))
        {

            $where = " userid = (select id from users where id='$userid' and reseller_id = '$resellerid') and DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";
        
        } else if(!empty($request->fromdate) && !empty($request->todate) && empty($request->userid)) {

            $where = " userid  IN (select id from users where reseller_id = '$resellerid') and DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";

        }else {

            $where = " userid = = (select id from users where id='$userid' and reseller_id = '$resellerid')";

        }
        

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        
        $columns = array(
            array( 'db' => 'smsid',   'dt' => 0 ),
            array( 'db' => 'sender_name',   'dt' => 1 ),
            array( 'db' => 'sms_type',   'dt' => 2 ),
            array( 'db' => 'name',   'dt' => 3 ),
            array( 'db' => 'contact',   'dt' => 4 ),
            array( 'db' => 'smscount',   'dt' => 5 ),
            array( 'db' => 'send_from',   'dt' => 6 ),
            array( 'db' => 'submitted_at',   'dt' => 7 ),
            array( 'db' => 'status',   'dt' => 8 ),
            array( 'db' => 'sms_content',   'dt' => 9 ),
            array( 'db' => 'totalcampaign',   'dt' => 10 ),
        );

        // SQL server connection information
        $sql_details = array(
            'user' => env('DB_USERNAME'),
            'pass' => env('DB_PASSWORD'),
            'db'   => env('DB_DATABASE'),
            'host' => env('DB_HOST')
        );


        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */

        

        echo json_encode(DataTableClass::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $where));

    }


    public function resellerClientArchiveConsulateSmsSendReport(Request $request)
    {
        
        $userid = $request->userid;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        $resellerid = Auth::guard('reseller')->user()->id;

        // DB table to use
        $table = 'archive_campaign';

        // Table's primary key
        $primaryKey = 'smsid';

      
        //$where = " DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";
        if (!empty($request->fromdate) && !empty($request->todate) && !empty($request->userid))
        {

            $where = " userid = (select id from users where id='$userid' and reseller_id = '$resellerid') and DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";
        
        } else if(!empty($request->fromdate) && !empty($request->todate) && empty($request->userid)) {

            $where = " userid  IN (select id from users where reseller_id = '$resellerid') and DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";

        }else {

            $where = " userid = = (select id from users where id='$userid' and reseller_id = '$resellerid')";

        }
        

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        
        $columns = array(
            array( 'db' => 'smsid',   'dt' => 0 ),
            array( 'db' => 'sender_name',   'dt' => 1 ),
            array( 'db' => 'sms_type',   'dt' => 2 ),
            array( 'db' => 'name',   'dt' => 3 ),
            array( 'db' => 'contact',   'dt' => 4 ),
            array( 'db' => 'smscount',   'dt' => 5 ),
            array( 'db' => 'send_from',   'dt' => 6 ),
            array( 'db' => 'submitted_at',   'dt' => 7 ),
            array( 'db' => 'status',   'dt' => 8 ),
            array( 'db' => 'sms_content',   'dt' => 9 ),
            array( 'db' => 'totalcampaign',   'dt' => 10 ),
        );

        // SQL server connection information
        $sql_details = array(
            'user' => env('DB_USERNAME'),
            'pass' => env('DB_PASSWORD'),
            'db'   => env('DB_DATABASE'),
            'host' => env('DB_HOST')
        );


        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */

        

        echo json_encode(DataTableClass::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $where));

    }


    public function todayGatewayErrorReport() {
        return view('smsview.clientsmsreport.today-gateway-error-report');
    }
    public function gatewayErrorReport(Request $request)
    {
        
        // DB table to use
        $table = 'sms_send_errors';

        // Table's primary key
        $primaryKey = 'id';
        
        $where = " DATE(created_at) ='".Carbon::today()."'";
        

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        
        $columns = array(
            array( 'db' => 'id',   'dt' => 0 ),
            array( 'db' => 'operator_type',   'dt' => 1 ),
            array( 'db' => 'senderid',   'dt' => 2 ),
            array( 'db' => 'error_description',   'dt' => 3 ),
            array( 'db' => 'created_at',   'dt' => 4 ),
        );

        // SQL server connection information
        $sql_details = array(
            'user' => env('DB_USERNAME'),
            'pass' => env('DB_PASSWORD'),
            'db'   => env('DB_DATABASE'),
            'host' => env('DB_HOST')
        );


        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */

        

        echo json_encode(DataTableClass::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $where));

    }


    public function rootClientCampaignMobile(Request $request)
    {
        $remarks = $request->remarks;

        $data = [];


        $records = DB::select(DB::raw("select u.name 'client_name',u.email 'client_email',s.sender_name,us.to_number,
        us.sms_type,us.sms_catagory,us.sms_content,us.number_of_sms,us.total_contacts,
        us.send_type,us.status,us.submitted_at
        from user_sent_smses us
        inner join users u
        on u.id = us.user_id
        inner join sms_senders s
        on s.id = us.user_sender_id
        where us.remarks like '%$remarks%'"));

        $rowid = 1;
        
        foreach($records as $record) {
                $data['data'][] = [
                    'rowid' => $rowid,
                    'senderid' => $record->sender_name,
                    'tonumber' => $record->to_number,
                    'smscount' => $record->number_of_sms,
                    'smscontent' => $record->sms_content,
                    'submittedat' => $record->submitted_at,
                    'sms_type' =>$record->sms_type,
                    'status' => $record->status,

                ];
                $rowid++;
            
        }

        return $data;
    }


    public function rootClientArchiveCampaignMobile(Request $request)
    {
        $remarks = $request->remarks;

        $data = [];


        $records = DB::select(DB::raw("select u.name 'client_name',u.email 'client_email',s.sender_name,us.to_number,
        us.sms_type,us.sms_catagory,us.sms_content,us.number_of_sms,us.total_contacts,
        us.send_type,us.status,us.submitted_at
        from archive_sent_smses us
        inner join users u
        on u.id = us.user_id
        inner join sms_senders s
        on s.id = us.user_sender_id
        where us.remarks like '%$remarks%'"));

        $rowid = 1;
        
        foreach($records as $record) {
                $data['data'][] = [
                    'rowid' => $rowid,
                    'senderid' => $record->sender_name,
                    'tonumber' => $record->to_number,
                    'smscount' => $record->number_of_sms,
                    'smscontent' => $record->sms_content,
                    'submittedat' => $record->submitted_at,
                    'sms_type' =>$record->sms_type,
                    'status' => $record->status,

                ];
                $rowid++;
            
        }

        return $data;
    }


    public function exportExcel(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Set document properties
            $spreadsheet->getProperties()->setCreator('Mohammed Minuddin Peal')
            ->setLastModifiedBy('Minuddin')
            ->setTitle('Client data export to excel')
            ->setSubject('Generate Excel')
            ->setDescription('Export data to Excel Work for me!');
        // add style to the header
            $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ),
            'borders' => array(
                'bottom' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => array('rgb' => '333333'),
                ),
            ),
            'fill' => array(
                'type'       => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation'   => 90,
                'startcolor' => array('rgb' => '0d0d0d'),
                'endColor'   => array('rgb' => 'f2f2f2'),
            ),
            );
            $spreadsheet->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray);
            // auto fit column to content
            foreach(range('A', 'G') as $columnID) {
                $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }
        // set the names of header cells
            $sheet->setCellValue('A1', 'SenderID');
            $sheet->setCellValue('B1', 'Contact');
            $sheet->setCellValue('C1', 'No.Of.Sms');
            $sheet->setCellValue('D1', 'Msg.Content');
            $sheet->setCellValue('E1', 'Date');
            $sheet->setCellValue('F1', 'SMS Type');
            $sheet->setCellValue('G1', 'Status');
            
            $remarks = $request->remarks;

            $data['data'] = [];


            $records = DB::select(DB::raw("select u.name 'client_name',u.email 'client_email',s.sender_name,us.to_number,
            us.sms_type,us.sms_catagory,us.sms_content,us.number_of_sms,us.total_contacts,
            us.send_type,us.status,us.submitted_at
            from user_sent_smses us
            inner join users u
            on u.id = us.user_id
            inner join sms_senders s
            on s.id = us.user_sender_id
            where us.remarks like '%$remarks%'"));

            foreach($records as $record) {
                    $data['data'][] = [
                        'senderid' => (string)$record->sender_name.' ',
                        'tonumber' => $record->to_number,
                        'smscount' => $record->number_of_sms,
                        'smscontent' => $record->sms_content,
                        'submittedat' => $record->submitted_at,
                        'sms_type' =>$record->sms_type,
                        'status' => $record->status == 1 ? 'Delivered': 'Failed',

                    ];
                
            }
            // Add some data
            $x = 2;
            foreach($data['data'] as $get){
                $sheet->setCellValue('A'.$x, (string)$get['senderid']);
                $sheet->setCellValue('B'.$x, $get['tonumber']);
                $sheet->setCellValue('C'.$x, $get['smscount']);
                $sheet->setCellValue('D'.$x, $get['smscontent']);
                $sheet->setCellValue('E'.$x, $get['submittedat']);
                $sheet->setCellValue('F'.$x, $get['sms_type']);
                $sheet->setCellValue('G'.$x, $get['status']);
            $x++;
            }
        //Create file excel.xlsx
        $filename = "export_".time().".xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);
        $content = file_get_contents($filename);
        header("Content-Disposition: attachment; filename=".$filename);

        unlink($filename);
        exit($content);
        //End Function index
    }

    public function exportArchiveSmsToExcel(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Set document properties
            $spreadsheet->getProperties()->setCreator('Mohammed Minuddin Peal')
            ->setLastModifiedBy('Minuddin')
            ->setTitle('Client data export to excel')
            ->setSubject('Generate Excel')
            ->setDescription('Export data to Excel Work for me!');
        // add style to the header
            $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ),
            'borders' => array(
                'bottom' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => array('rgb' => '333333'),
                ),
            ),
            'fill' => array(
                'type'       => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation'   => 90,
                'startcolor' => array('rgb' => '0d0d0d'),
                'endColor'   => array('rgb' => 'f2f2f2'),
            ),
            );
            $spreadsheet->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray);
            // auto fit column to content
            foreach(range('A', 'G') as $columnID) {
                $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }
        // set the names of header cells
            $sheet->setCellValue('A1', 'SenderID');
            $sheet->setCellValue('B1', 'Contact');
            $sheet->setCellValue('C1', 'No.Of.Sms');
            $sheet->setCellValue('D1', 'Msg.Content');
            $sheet->setCellValue('E1', 'Date');
            $sheet->setCellValue('F1', 'SMS Type');
            $sheet->setCellValue('G1', 'Status');
            
            $remarks = $request->remarks;

            $data['data'] = [];


            $records = DB::select(DB::raw("select u.name 'client_name',u.email 'client_email',s.sender_name,us.to_number,
            us.sms_type,us.sms_catagory,us.sms_content,us.number_of_sms,us.total_contacts,
            us.send_type,us.status,us.submitted_at
            from archive_sent_smses us
            inner join users u
            on u.id = us.user_id
            inner join sms_senders s
            on s.id = us.user_sender_id
            where us.remarks like '%$remarks%'"));

            foreach($records as $record) {
                    $data['data'][] = [
                        'senderid' => (string)$record->sender_name.' ',
                        'tonumber' => $record->to_number.' ',
                        'smscount' => $record->number_of_sms,
                        'smscontent' => $record->sms_content,
                        'submittedat' => $record->submitted_at,
                        'sms_type' =>$record->sms_type,
                        'status' => $record->status == 1 ? 'Delivered': 'Failed',

                    ];
                
            }
            // Add some data
            $x = 2;
            foreach($data['data'] as $get){
                $sheet->setCellValue('A'.$x, (string)$get['senderid']);
                $sheet->setCellValue('B'.$x, $get['tonumber']);
                $sheet->setCellValue('C'.$x, $get['smscount']);
                $sheet->setCellValue('D'.$x, $get['smscontent']);
                $sheet->setCellValue('E'.$x, $get['submittedat']);
                $sheet->setCellValue('F'.$x, $get['sms_type']);
                $sheet->setCellValue('G'.$x, $get['status']);
            $x++;
            }
        //Create file excel.xlsx
        $filename = "export_".time().".xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);
        $content = file_get_contents($filename);
        header("Content-Disposition: attachment; filename=".$filename);

        unlink($filename);
        exit($content);
        //End Function index
    }

    public function totalClientSmsSendReport(Request $request)
    {
        $userid = $request->user()->id;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms,count(remarks) totalcampaign
        from user_sent_smses
        where user_id = '$userid'
        and DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
        and status = 1

        "));

        return $records[0]->totalsms;
    }

    public function totalClientSmsCampaign(Request $request)
    {
        $userid = $request->user()->id;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms,count(remarks) totalcampaign
        from user_sent_smses
        where user_id = '$userid'
        and DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
        and status = 1

        "));

        return $records[0]->totalcampaign;
    }


    public function totalClientArchiveSmsSendReport(Request $request)
    {
        $userid = $request->user()->id;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms,count(remarks) totalcampaign
        from archive_sent_smses
        where user_id = '$userid'
        and DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
        and status = 1

        "));

        return $records[0]->totalsms;
    }

    public function totalClientArchiveSmsCampaign(Request $request)
    {
        $userid = $request->user()->id;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms,count(remarks) totalcampaign
        from archive_sent_smses
        where user_id = '$userid'
        and DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
        and status = 1

        "));

        return $records[0]->totalcampaign;
    }


    public function clientSmsSentConsulateReport(Request $request)
    {
        $userid = $request->user()->id;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        $records = DB::select(DB::raw("select uc.id 'smscountid',u.name,u.email,uc.sms_count,
                                                uc.campaing_name,uc.sms_category,uc.month_name,
                                                uc.year_name,uc.owner_type,uc.created_at,
                                                CASE WHEN uc.owner_type = 'root' then 'root'
                                                    WHEN uc.owner_type = 'reseller' then 
                                                                    (select rs.name 
                                                                    from resellers rs 
                                                                    inner join user_count_smses ucs
                                                                    on rs.id = ucs.owner_id limit 1) END 'owner'
                                                from user_count_smses uc
                                                inner join users u
                                                on uc.user_id = u.id
                                                where uc.user_id = '$userid'
                                                and DATE(uc.created_at) BETWEEN '$fromdate' and '$todate'"));

        $rowid = 1;
        foreach($records as $record) {
                $data['data'][] = [
                    'rowid' => $rowid,
                    'name' => $record->name,
                    'email' => $record->email,
                    'campaingname' => $record->campaing_name,
                    'smscount' => $record->sms_count,
                    'smscategory' =>$record->sms_category,
                    'month' => $record->month_name,
                    'year' => $record->year_name,
                    'ownertype' => $record->owner_type,
                    'owner' => $record->owner,
                    'submittedat' => $record->created_at,

                ];
                $rowid++;
            
        }

        return $data;
    }


    public function dlrReport(Request $request)
    {
        $userid = $request->user()->id;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        

        return $this->smsreport->successDlr([
            'fromdate' => $fromdate,
            'todate' => $todate,
            'userid' => $userid
        ]);
    }

    public function campaignReview(Request $request)
    {
        $userid = $request->user()->id;

        $campaign = $request->campaign;

        $query = DB::select(DB::raw("select us.id 'sentsmsid',us.remarks campaign, u.name 'client_name',u.email 'client_email',s.sender_name,us.to_number,
        us.sms_type,us.sms_catagory,us.sms_content,us.number_of_sms,us.total_contacts,
        us.send_type,us.status,us.submitted_at
        from user_sent_smses us
        inner join users u
        on u.id = us.user_id
        inner join sms_senders s
        on s.id = us.user_sender_id
        where us.user_id = '$userid'
        and us.remarks = '$campaign'"));

        $rowid = 1;
        $data = [];
        foreach($query as $record) {
            
            foreach(explode(",",$record->to_number) as $contact) {
                $data['data'][] = [
                    'rowid' => $rowid,
                    'campaign' => $record->campaign,
                    'senderid' => $record->sender_name,
                    'contact' => $contact,
                    'smscontent' => $record->sms_content,
                    'noofsms' => $record->number_of_sms,
                    'totalcontact' => $record->total_contacts,
                    'status' => $record->status == 1 ? 'Delivered': 'Faild',
                    'submittedat' => $record->submitted_at,

                ];
                $rowid++;
            }

            
        }

        return json_encode($data);
    }



    public function clientSmsSentTotalConsulateReport(Request $request)
    {
        $userid = $request->user()->id;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        $records = DB::select(DB::raw("select sum(sms_count) 'totalsms'from user_count_smses where user_id = '32' and DATE(created_at) BETWEEN '$fromdate' and '$todate'"));
        
        return $records;
    }



    public function rootClientSmsSendReport(Request $request)
    {
        // DB table to use
        $table = 'detail_dlr';

        // Table's primary key
        $primaryKey = 'sentrecid';

        //$where = " DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";
        if (!empty($request->fromdate) && !empty($request->todate) && !empty($request->userid))
        {

            $where = " userid = '".$request->userid."' and DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";
        
        } else if(!empty($request->fromdate) && !empty($request->todate) && empty($request->userid)) {

            $where = " DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";

        }else {

            $where = " userid = '".$request->userid."'";

        }

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        
        $columns = array(
            array( 'db' => 'sentrecid', 'dt' => 0 ),
            array( 'db' => 'smsid',   'dt' => 1 ),
            array( 'db' => 'client_name', 'dt' => 2 ),
            array( 'db' => 'client_email',  'dt' => 3 ),
            array( 'db' => 'sender_name',   'dt' => 4 ),
            array( 'db' => 'contact',   'dt' => 5 ),
            array( 'db' => 'sms_type',   'dt' => 6 ),
            array( 'db' => 'sms_catagory',   'dt' => 7 ),
            array( 'db' => 'smscount',   'dt' => 8 ),
            array( 'db' => 'send_from',   'dt' => 9 ),
            array( 'db' => 'submitted_at',   'dt' => 10 ),
            array( 'db' => 'status',   'dt' => 11 ),
            array( 'db' => 'sms_content',   'dt' => 12 )
        );

        // SQL server connection information
        $sql_details = array(
            'user' => env('DB_USERNAME'),
            'pass' => env('DB_PASSWORD'),
            'db'   => env('DB_DATABASE'),
            'host' => env('DB_HOST')
        );


        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */

        

        echo json_encode(DataTableClass::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $where));
    }

    public function totalRootClientSmsSendReport(Request $request)
    {
        $userid = $request->userid;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        if (!empty($userid) && isset($userid)) {
            $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms
            from user_sent_smses
            where user_id = '$userid'
            and DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
            and status = 1

            "));

        } else {
            $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms
            from user_sent_smses
            where DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
            and status = 1

            "));
        }

        return $records[0]->totalsms;

        
    }

    public function totalRootClientSmsCampaign(Request $request)
    {
        $userid = $request->userid;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        if (!empty($userid) && isset($userid)) {
            $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms,count(remarks) totalcampaign
            from user_sent_smses
            where user_id = '$userid'
            and DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
            and status = 1

            "));

        } else {
            $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms,count(remarks) totalcampaign
            from user_sent_smses
            where DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
            and status = 1

            "));
        }

        return $records[0]->totalcampaign;

        
    }



    public function totalRootClientArchiveSmsSendReport(Request $request)
    {
        $userid = $request->userid;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        if (!empty($userid) && isset($userid)) {
            $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms
            from archive_sent_smses
            where user_id = '$userid'
            and DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
            and status = 1

            "));

        } else {
            $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms
            from archive_sent_smses
            where DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
            and status = 1

            "));
        }

        return $records[0]->totalsms;

        
    }

    public function totalRootClientArchiveSmsCampaign(Request $request)
    {
        $userid = $request->userid;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        if (!empty($userid) && isset($userid)) {
            $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms,count(remarks) totalcampaign
            from archive_sent_smses
            where user_id = '$userid'
            and DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
            and status = 1

            "));

        } else {
            $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms,count(remarks) totalcampaign
            from archive_sent_smses
            where DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
            and status = 1

            "));
        }

        return $records[0]->totalcampaign;

        
    }



    
    public function rootClientSmsSentConsulateReport(Request $request)
    {
        $userid = $request->userid;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        if (!empty($userid) && isset($userid)) {
            $records = DB::select(DB::raw("select uc.id 'smscountid',u.name,u.email,uc.sms_count,
                                                  uc.campaing_name,uc.sms_category,uc.month_name,
                                                  uc.year_name,uc.owner_type,uc.created_at,
                                                    CASE WHEN uc.owner_type = 'root' then 'root'
                                                        WHEN uc.owner_type = 'reseller' then 
                                                                        (select rs.name 
                                                                        from resellers rs 
                                                                        inner join user_count_smses ucs
                                                                        on rs.id = ucs.owner_id limit 1) END 'owner'
                                                    from user_count_smses uc
                                                    inner join users u
                                                    on uc.user_id = u.id
                                                    where uc.user_id = '$userid'
                                                    and DATE(uc.created_at) BETWEEN '$fromdate' and '$todate'"));

        } else {
            $records = DB::select(DB::raw("select uc.id 'smscountid',u.name,u.email,uc.sms_count,
                                                  uc.campaing_name,uc.sms_category,uc.month_name,
                                                  uc.year_name,uc.owner_type,uc.owner_type,uc.created_at,
                                                    CASE WHEN uc.owner_type = 'root' then 'root'
                                                        WHEN uc.owner_type = 'reseller' then 
                                                                        (select rs.name 
                                                                        from resellers rs 
                                                                        inner join user_count_smses ucs
                                                                        on rs.id = ucs.owner_id limit 1) END 'owner'
                                                    from user_count_smses uc
                                                    inner join users u
                                                    on uc.user_id = u.id
                                                    where DATE(uc.created_at) BETWEEN '$fromdate' and '$todate'"));
        }

        $rowid = 1;
        foreach($records as $record) {
                $data['data'][] = [
                    'rowid' => $rowid,
                    'name' => $record->name,
                    'email' => $record->email,
                    'campaingname' => $record->campaing_name,
                    'smscount' => $record->sms_count,
                    'smscategory' =>$record->sms_category,
                    'month' => $record->month_name,
                    'year' => $record->year_name,
                    'ownertype' => $record->owner_type,
                    'owner' => $record->owner,
                    'submittedat' => $record->created_at,

                ];
                $rowid++;
            
        }

        return $data;
    }

    public function rootClientSmsSentTotalConsulateReport(Request $request)
    {
        $userid = $request->userid;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        if (!empty($userid) && isset($userid)) {
            $records = DB::select(DB::raw("select sum(sms_count) 'totalsms'
                                                    from user_count_smses
                                                    where user_id = '$userid'
                                                    and DATE(created_at) BETWEEN '$fromdate' and '$todate'"));

        } else {
            $records = DB::select(DB::raw("select sum(sms_count) 'totalsms'
                                            from user_count_smses
                                            where DATE(created_at) BETWEEN '$fromdate' and '$todate'"));
        }

        

        return $records;
    }

    public function clientFaildSmsSendReport(Request $request)
    {
        $userid = $request->user()->id;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        $records = DB::select(DB::raw("select us.id 'sentsmsid',u.name 'client_name',u.email 'client_email',s.sender_name,us.to_number,
        us.sms_type,us.sms_catagory,us.sms_content,us.number_of_sms,us.total_contacts,
        us.send_type,us.status,us.submitted_at
        from user_sent_smses us
        inner join users u
        on u.id = us.user_id
        inner join sms_senders s
        on s.id = us.user_sender_id
        where us.user_id = '$userid'
        and DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
        and us.status = 0

        "));

        $rowid = 1;
        foreach($records as $record) {
            
            foreach(explode(",",$record->to_number) as $contact) {
                $data['data'][] = [
                    'rowid' => $rowid,
                    'name' => $record->client_name,
                    'email' => $record->client_email,
                    'senderid' => $record->sender_name,
                    'contact' => $contact,
                    'smstype' => $record->sms_type,
                    'smscategory' =>$record->sms_catagory,
                    'smscontent' => $record->sms_content,
                    'noofsms' => $record->number_of_sms,
                    'totalcontact' => $record->total_contacts,
                    'sendfrom' => $record->send_type,
                    'submittedat' => $record->submitted_at,
                    'status' => $record->status == 1 ? 'Delivered': 'Faild'

                ];
                $rowid++;
            }

            
        }

        return $data;
    }


    public function resellerClientSmsSendReport(Request $request)
    {
        $userid = $request->userid;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        $resellerid = Auth::guard('reseller')->user()->id;

        // DB table to use
        $table = 'detail_dlr';

        // Table's primary key
        $primaryKey = 'sentrecid';

        //$where = " DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";
        if (!empty($request->fromdate) && !empty($request->todate) && !empty($request->userid))
        {

            $where = " userid = (select id from users where id='$userid' and reseller_id = '$resellerid') and DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";
        
        } else if(!empty($request->fromdate) && !empty($request->todate) && empty($request->userid)) {

            $where = " userid  IN (select id from users where reseller_id = '$resellerid') and DATE(submitted_at) Between '".$request->fromdate."' and '".$request->todate."'";

        }else {

            $where = " userid = = (select id from users where id='$userid' and reseller_id = '$resellerid')";

        }

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        
        $columns = array(
            array( 'db' => 'sentrecid', 'dt' => 0 ),
            array( 'db' => 'smsid',   'dt' => 1 ),
            array( 'db' => 'client_name', 'dt' => 2 ),
            array( 'db' => 'client_email',  'dt' => 3 ),
            array( 'db' => 'sender_name',   'dt' => 4 ),
            array( 'db' => 'contact',   'dt' => 5 ),
            array( 'db' => 'sms_type',   'dt' => 6 ),
            array( 'db' => 'sms_catagory',   'dt' => 7 ),
            array( 'db' => 'smscount',   'dt' => 8 ),
            array( 'db' => 'send_from',   'dt' => 9 ),
            array( 'db' => 'submitted_at',   'dt' => 10 ),
            array( 'db' => 'status',   'dt' => 11 ),
            array( 'db' => 'sms_content',   'dt' => 12 )
        );

        // SQL server connection information
        $sql_details = array(
            'user' => env('DB_USERNAME'),
            'pass' => env('DB_PASSWORD'),
            'db'   => env('DB_DATABASE'),
            'host' => env('DB_HOST')
        );


        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */

        

        echo json_encode(DataTableClass::complex( $_GET, $sql_details, $table, $primaryKey, $columns, null, $where));
    }


    public function totalResellerClientSmsSendReport(Request $request)
    {
        $userid = $request->userid;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $resellerid = Auth::guard('reseller')->user()->id;

        $data = [];

        if (!empty($userid) && isset($userid)) {
            $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms,count(remarks) totalcampaign
            from user_sent_smses
            where user_id = (select id from users where id='$userid' and reseller_id = '$resellerid')
            and DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
            and status = 1

            "));

        } else {
            $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms,count(remarks) totalcampaign
            from user_sent_smses
            where user_id IN (select id from users where reseller_id = '$resellerid')  and DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
            and status = 1

            "));
        }

        return $records[0]->totalsms;

    }

    public function totalResellerClientSmsCampaign(Request $request)
    {
        $userid = $request->userid;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $resellerid = Auth::guard('reseller')->user()->id;

        $data = [];

        if (!empty($userid) && isset($userid)) {
            $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms,count(remarks) totalcampaign
            from user_sent_smses
            where user_id = (select id from users where id='$userid' and reseller_id = '$resellerid')
            and DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
            and status = 1

            "));

        } else {
            $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms,count(remarks) totalcampaign
            from user_sent_smses
            where user_id IN (select id from users where reseller_id = '$resellerid')  and DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
            and status = 1

            "));
        }

        return $records[0]->totalcampaign;

    }


    public function totalResellerClientArchiveSmsSendReport(Request $request)
    {
        $userid = $request->userid;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $resellerid = Auth::guard('reseller')->user()->id;

        $data = [];

        if (!empty($userid) && isset($userid)) {
            $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms,count(remarks) totalcampaign
            from archive_sent_smses
            where user_id = (select id from users where id='$userid' and reseller_id = '$resellerid')
            and DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
            and status = 1

            "));

        } else {
            $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms,count(remarks) totalcampaign
            from archive_sent_smses
            where user_id IN (select id from users where reseller_id = '$resellerid')  and DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
            and status = 1

            "));
        }

        return $records[0]->totalsms;

    }

    public function totalResellerClientArchiveSmsCampaign(Request $request)
    {
        $userid = $request->userid;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $resellerid = Auth::guard('reseller')->user()->id;

        $data = [];

        if (!empty($userid) && isset($userid)) {
            $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms,count(remarks) totalcampaign
            from archive_sent_smses
            where user_id = (select id from users where id='$userid' and reseller_id = '$resellerid')
            and DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
            and status = 1

            "));

        } else {
            $records = DB::select(DB::raw("select sum(number_of_sms*total_contacts) as totalsms,count(remarks) totalcampaign
            from archive_sent_smses
            where user_id IN (select id from users where reseller_id = '$resellerid')  and DATE(submitted_at) BETWEEN '$fromdate' and '$todate'
            and status = 1

            "));
        }

        return $records[0]->totalcampaign;

    }

    
    public function resellerClientSmsSentConsulateReport(Request $request)
    {
        $userid = $request->userid;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        $resellerid = Auth::guard('reseller')->user()->id;

        if (!empty($userid) && isset($userid)) {
            $records = DB::select(DB::raw("select uc.id 'smscountid',u.name,u.email,uc.sms_count,
                                                  uc.campaing_name,uc.sms_category,uc.month_name,
                                                  uc.year_name,uc.owner_type,uc.created_at,
                                                    CASE WHEN uc.owner_type = 'root' then 'root'
                                                        WHEN uc.owner_type = 'reseller' then 
                                                                        (select rs.name 
                                                                        from resellers rs 
                                                                        inner join user_count_smses ucs
                                                                        on rs.id = ucs.owner_id limit 1) END 'owner'
                                                    from user_count_smses uc
                                                    inner join users u
                                                    on uc.user_id = u.id
                                                    where uc.user_id = (select id from users where id='$userid' and reseller_id = '$resellerid')
                                                    and DATE(uc.created_at) BETWEEN '$fromdate' and '$todate'"));

        } else {
            $records = DB::select(DB::raw("select uc.id 'smscountid',u.name,u.email,uc.sms_count,
                                                  uc.campaing_name,uc.sms_category,uc.month_name,
                                                  uc.year_name,uc.owner_type,uc.owner_type,uc.created_at,
                                                    CASE WHEN uc.owner_type = 'root' then 'root'
                                                        WHEN uc.owner_type = 'reseller' then 
                                                                        (select rs.name 
                                                                        from resellers rs 
                                                                        inner join user_count_smses ucs
                                                                        on rs.id = ucs.owner_id limit 1) END 'owner'
                                                    from user_count_smses uc
                                                    inner join users u
                                                    on uc.user_id = u.id
                                                    where uc.user_id IN (select id from users where reseller_id = '$resellerid')  
                                                    and DATE(uc.created_at) BETWEEN '$fromdate' and '$todate'"));
        }

        $rowid = 1;
        foreach($records as $record) {
                $data['data'][] = [
                    'rowid' => $rowid,
                    'name' => $record->name,
                    'email' => $record->email,
                    'campaingname' => $record->campaing_name,
                    'smscount' => $record->sms_count,
                    'smscategory' =>$record->sms_category,
                    'month' => $record->month_name,
                    'year' => $record->year_name,
                    'ownertype' => $record->owner_type,
                    'owner' => $record->owner,
                    'submittedat' => $record->created_at,

                ];
                $rowid++;
            
        }

        return $data;
    }

    public function resellerClientSmsSentTotalConsulateReport(Request $request)
    {
        $userid = $request->userid;

        $fromdate = $request->fromdate;

        $todate = $request->todate;

        $data = [];

        $resellerid = Auth::guard('reseller')->user()->id;

        if (!empty($userid) && isset($userid)) {
            $records = DB::select(DB::raw("select sum(sms_count) 'totalsms'
                                                    from user_count_smses
                                                    where user_id = (select id from users where id='$userid' and reseller_id = '$resellerid')
                                                    and DATE(created_at) BETWEEN '$fromdate' and '$todate'"));

        } else {
            $records = DB::select(DB::raw("select sum(sms_count) 'totalsms'
                                            from user_count_smses
                                            where user_id IN (select id from users where reseller_id = '$resellerid') 
                                            and DATE(created_at) BETWEEN '$fromdate' and '$todate'"));
        }

        

        return $records;
    }


    public function clientSmsReport()
    {
        $clients = $this->client->activeClients();
        return view('smsview.clientsmsreport.client-sms-report',compact('clients'));
    }

    

    public function clientArchiveSmsConsulateReport()
    {
        return view('smsview.clientsmsreport.client-archive-sms-consulate-report');
    }

    public function rootClientSmsConsulateReport()
    {
        $clients = $this->client->activeClients();
        return view('smsview.clientsmsreport.root-client-sms-consulate-report',compact('clients'));
    }

    public function rootClientArchiveSmsConsulateReport()
    {
        $clients = $this->client->activeClients();
        return view('smsview.clientsmsreport.root-client-archive-sms-consulate-report',compact('clients'));
    }

    public function resellerClientSmsConsulateReport()
    {
        $resellerid = Auth::guard('reseller')->user()->id;
        $clients = $this->client->activeResellerClients($resellerid);
        return view('smsview.clientsmsreport.reseller-client-sms-consulate-report',compact('clients'));
    }

    public function resellerClientArchiveSmsConsulateReport()
    {
        $resellerid = Auth::guard('reseller')->user()->id;
        $clients = $this->client->activeResellerClients($resellerid);
        return view('smsview.clientsmsreport.reseller-client-archive-sms-consulate-report',compact('clients'));
    }

    public function clientFaildSmsReport()
    {
        return view('smsview.clientsmsreport.client-faild-sms-report');
    }


    public function rootClientSmsReport()
    {
        $clients = $this->client->activeClients();
        return view('smsview.clientsmsreport.root-clients-sms-report',compact('clients'));
    }

    public function rootClientSendSmsCount()
    {
        $clients = $this->client->activeClients();
        return view('smsview.clientsmsreport.root-clients-send-sms-count',compact('clients'));
    }

    public function clientSendSmsCount()
    {
        $clients = $this->client->activeClients();
        return view('smsview.clientsmsreport.clients-send-sms-count',compact('clients'));
    }

    public function resellerClientSmsReport()
    {
        $resellerid = Auth::guard('reseller')->user()->id;
        $clients = $this->client->activeResellerClients($resellerid);
        return view('smsview.clientsmsreport.reseller-clients-sms-report',compact('clients'));
    }

    public function resellerClientSendSmsCount()
    {
        $resellerid = Auth::guard('reseller')->user()->id;
        $clients = $this->client->activeResellerClients($resellerid);
        return view('smsview.clientsmsreport.reseller-clients-send-sms-count',compact('clients'));
    }
}
