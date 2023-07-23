<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Core\Templates\Template;
use App\Http\Resources\TemplateResource;
use App\Template as AppTemplate;
use Illuminate\Support\Str;
use App\Core\SmsSend\SmsSend;
use App\Core\ProductSales\ProductSales;
use App\Core\UserCountSms\UserCountSms;
use App\Core\HandleFile\HandleFile;
use App\Http\Resources\SmsSenderIdResource;
use GuzzleHttp\Client;
use App\CheckRuntimeBalance;
use App\Gateway;
use Carbon\Carbon;
use DB;

class TemplateController extends Controller
{
    protected $template;

    protected $templatecontent;

    protected $handlefile;

    protected $extension;

    protected $file;

    protected $contactarr;
    
    protected $product;
    
    protected $consumesms;

    /**
     * sms message count
     *
     * @var string
     */
    protected $messagecount;

    /**
     * sms message type
     *
     * @var string
     */
    protected $messagetype;

    /**
     * SmsSend service
     *
     * @var App\Core\SmsSend\SmsSendDetails
     */
    protected $smssend;

    public function __construct(
        Template $template,
        HandleFile $handlefile,
        SmsSend $smssend,
        ProductSales $product,
        UserCountSms $consumesms
    )
    {
        $this->template = $template;

        $this->handlefile = $handlefile;

        $this->smssend = $smssend;

        $this->product = $product;

        $this->consumesms = $consumesms;

        $this->middleware('auth:root,web,manager,reseller');
    }

    public function manageTemplate()
    {
        return view('smsview.template.manage-template');
    }

    public function clientTemplate()
    {
        return view('smsview.template.client-template');
    }

    public function manageClientTemplate(Request $request)
    {
        return $this->template->clientTemplate([
            'userid' => !empty($request->userid) ? $request->userid : @Auth::guard('web')->user()->id,
            'usertype' => 'client'
        ]);
    }

    public function manageRootTemplate(Request $request)
    {
        return $this->template->rootTemplate();
    }


    public function showApprovedTemplate($tempid) {
        
        return $this->template->showApprovedTemplate($tempid);
    }

    public function approvedTemplteUploadedFile(Request $request) {
        
        $this->file = $this->handlefile->addFile($request);

        $extension = $this->handlefile->getFileExtension();

        
        $heads = [];
        $dataarr = [];
        
        $templatearr = [];
        $contactarr = [];
        if ( $extension == 'xls' ||  $extension == 'xlsx')
        {

            $contents = $this->handlefile->readXlsFile();

           

            $f = 0;

            foreach($contents as $key => $content) {
                
                if ($f == 0) {
                    foreach($content as $data) {

                        $heads[] = trim(Str::lower($data));
                        
                    }

                    $f = 1;
                } else {
                        $dataarr[] = array_combine($heads,array_values($content));
                }
                
            }



            $x = 0;


            if (preg_match_all("/{(.*?)}/", Str::lower($request->msgcontent), $m)) {

                $y = 0;

                foreach($dataarr as $data) {

                    foreach ($m[1] as $i => $varname) {

                        if ($x == 0) {
                            if (! in_array(Str::lower($varname), $heads)) {

                                return response()->json(['errmsg' => 'Column not match'], 406);
                            }

                            if (@$heads[$i] == 'mobile') {
                                $this->contactarr = $data['mobile'];
                            }

                            $this->templatecontent = str_replace(Str::lower($m[1][$i]), Str::lower($data[$m[1][$i]]), Str::lower($request->msgcontent));
                            
                            $x = 1;

                        } else {

                            if (! in_array($varname, $heads)) {

                                return response()->json(['errmsg' => 'Column not match'], 406);
                            }

                            if (@$heads[$i] == 'mobile') {
                                $this->contactarr = $data['mobile'];
                            }

                            $this->templatecontent = str_replace(Str::lower($m[1][$i]), Str::lower($data[$m[1][$i]]), Str::lower($this->templatecontent));
                        }
                    }

                    $templatearr[] = [
                        'contact' => $this->contactarr,
                        'message' => preg_replace('/\{?\}?+/','',$this->templatecontent)
                    ];

                    $y++;

                    $x = 0;
                }
            } else {
                 foreach($dataarr as $data) {
                    $templatearr[] = [
                        'contact' => $data['mobile'],
                        'message' => $request->msgcontent
                    ];
                 }
            }

        }

        

        return $templatearr;
    }


    public function manageSmsMessage(Request $request)
    {
        if (!$request->has('message') ||
            !$request->has('senderid')||
            !$request->has('contact_number')
        ) {
            return response()->json(['errmsg' => 'Required field is empty, message content, senderid & contact number are required parameter'],406);
        }

        //return $this->approvedTemplteUploadedFile($request);
        
         /**This section will function after ensure the job worker setup in the server 
         dispatch(new UserSmsSetupJob([
            'remarks' => $campaing,
            'user_id' => Auth::guard('web')->user()->id,
            'user_sender_id' => $clientsenderids->id,
            'contacts' => $contacts,
            'sms_type' => $this->messagetype,
            'sms_catagory' => $senderidtype,
            'sms_content' => $request->message,
            'number_of_sms' => $this->messagecount,
            'total_contacts' => $totalValidContact,
            'send_type' => 'smsadmin',
            'contact_group_id' => 0,
            'status' => false,
            'submitted_at' => Carbon::now(),
        ]))->delay(now()->addSeconds(1));
            
        return response()->json(['msg' => 'Campaing setup successfully done'], 200);
        */

        $smssentdata = [];

        $contacts = [];

        $operator_contacts = [];

        foreach(array_unique($this->approvedTemplteUploadedFile($request),SORT_REGULAR) as $contact_number)
        {

            //$campaing = !empty($request->cam_name) ? $request->cam_name."-".Auth::guard('web')->user()->id : 'campaing-'.Auth::guard('web')->user()->id.date("d").time();
            $campaing = 'campaing-'.Auth::guard('web')->user()->id.date("d").time();

            $clientsenderids = $this->smssend->getSenderIdInformationBySenderName($request->senderid);

            if (! $clientsenderids instanceof SmsSenderIdResource)
            {
                return response()->json(['errmsg' => $clientsenderids->getData()->errmsg], 406);
            }

            $gateways = json_decode($clientsenderids->gateway_info);

            if (empty($gateways))
            {
                $gateways = $clientsenderids;
            }


            $contacts[] = $contact_number['contact'];

            $this->messagecount = $this->smssend->manageSmsMessageCount($contact_number['message']);

            $this->messagetype = $this->smssend->smsMessageType($contact_number['message']);

            $senderidtype = $this->smssend->getSenderIdType(['senderid' => $request->senderid]);

            $clientid = Auth::guard('web')->user();

            $clientmasksmsbal = $this->product->getSmsBalanceByCategory($clientid->id,'mask');
            $clientnonmasksmsbal = $this->product->getSmsBalanceByCategory($clientid->id,'nomask');
            $clientvoicemsbal = $this->product->getSmsBalanceByCategory($clientid->id,'voice');

            $totalmaskbal = ($clientmasksmsbal-$this->consumesms->totalConsumeMaskBalance($clientid->id));
            $totalnonmaskbal = ($clientnonmasksmsbal-$this->consumesms->totalConsumeNonMaskBalance($clientid->id));
            $totalvoicebal = ($clientvoicemsbal-$this->consumesms->totalConsumeVoiceBalance($clientid->id));


            $root_userid = Auth::guard('web')->user()->root_user_id;

            $reseller_id = Auth::guard('web')->user()->reseller_id;

            $totalSms = 0;

            //Single contact number start
            if ($request->numbertype == 'single')
            {
                $contacts = is_array($request->contact_number) ? $request->contact_number : explode(",",str_replace("\n",",",str_replace(" ",",",$request->contact_number)));

                $totalSms = $this->messagecount*count($contacts);

                if ($totalmaskbal < $totalSms && $senderidtype == 'mask')
                {
                    return response()->json(['errmsg' => "Insufficient mask sms balance"], 406);
                }

                if ($totalnonmaskbal < $totalSms && $senderidtype == 'nomask')
                {
                    return response()->json(['errmsg' => "Insufficient nonmask sms balance"], 406);
                }

                if ($totalvoicebal < $totalSms && $senderidtype == 'voice')
                {
                    return response()->json(['errmsg' => "Insufficient voice sms balance"], 406);
                }

            } else {
                $contacts = $contacts;//$request->numbertype == 'contgroup' ? $this->validMobile($request) : $this->validMobileFromFile($request);

                //$totalValidContact = $this->totalValidContact;

                //$totalSms = ($totalValidContact*$this->messagecount);

                $totalSms = $this->messagecount*count($contacts);

                if ($totalmaskbal < $totalSms && $senderidtype == 'mask')
                {
                    return response()->json(['errmsg' => "Insufficient mask sms balance"], 406);
                }

                if ($totalnonmaskbal < $totalSms && $senderidtype == 'nomask')
                {
                    return response()->json(['errmsg' => "Insufficient nonmask sms balance"], 406);
                }

                if ($totalvoicebal < $totalSms && $senderidtype == 'voice')
                {
                    return response()->json(['errmsg' => "Insufficient voice sms balance"], 406);
                }
            }
            
            $validprefix =  ["88017","88014","88013","88015","88018","88016","88019"];

            $checkcontact = Str::substr($contact_number['contact'],0,2);
            $checkcontactwithplus = Str::substr($contact_number['contact'],0,3);

            //Covert number in format like 01716xxxxxx if start with 88
            $contact_number_n = '';
            $opt = '';
            if ($checkcontact == 88 || $checkcontactwithplus == '+88')
            {
                $contact_number_n = $checkcontact == 88 ? Str::substr($contact_number['contact'],0,13) : Str::substr($contact_number['contact'],1,13); 
                $opt = Str::substr($contact_number_n,0,5);
            } else {
                $contact_number_n = $contact_number['contact'];
                $opt = '88'.Str::substr($contact_number_n,0,3);
            }

            $dippingarr = $contact_number_n;

            $contact_length = Str::length($contact_number_n);


            if (Auth::guard('web')->user()->live_dipping == true) {

                    $dippingoperator = ["Grameenphone","Banglalink","Airtel","Robi","TeleTalk"];

                    $client = new Client([
                        'verify' => false
                    ]);
            
                    $clientresponse = $client->request('GET','http://api.lexiconbd.net/lexiconbdmnp.aspx',[
                        'query' => [ 
                            'apikey' => 'b0652a5086fbe9c03039c7d341c9e73a12863165',
                            'number' => $dippingarr,
                        ]
                    ]);
            
                    $responsedata =  json_decode($clientresponse->getBody());

                    $rescollect = collect($responsedata);

                    DB::table("lexicon_dipping_logs")->insert([
                        'code' => $rescollect['data'][0]->code,
                        'mobile' => $rescollect['data'][0]->mobile,
                        'prefix' => $rescollect['data'][0]->prefix,
                        'operator' => $rescollect['data'][0]->operator,
                        'status' => $rescollect['data'][0]->status,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                    //return $rescollect['data'][0]->operator;

                    $shortoperator = '';


                    if (in_array(trim($rescollect['data'][0]->operator), $dippingoperator)) {
                        
                        if (trim($rescollect['data'][0]->operator) == "Grameenphone") {
                            if ($rescollect['data'][0]->prefix == "88017") {
                                $shortoperator = 'gp';
                            }

                            if ($rescollect['data'][0]->prefix == "88013") {
                                $shortoperator = 'gpx';
                            }
                        }

                        if (trim($rescollect['data'][0]->operator) == "Banglalink") {
                            if ($rescollect['data'][0]->prefix == "88019") {
                                $shortoperator = 'blink';
                            }

                            if ($rescollect['data'][0]->prefix == "88014") {
                                $shortoperator = 'blx';
                            }
                        }

                        if (trim($rescollect['data'][0]->operator) == "Airtel") {
                            $shortoperator = 'airtel';
                        }

                        if (trim($rescollect['data'][0]->operator) == "Robi") {
                            $shortoperator = 'robi';
                        }

                        if (trim($rescollect['data'][0]->operator) == "TeleTalk") {
                            $shortoperator = 'teletalk';
                        }

                        $operator_contacts[$shortoperator][] = [
                            'contact' => ($shortoperator == 'gp' || $shortoperator == 'gpx') && $contact_length == 13 ? Str::substr($contact_number_n,2,$contact_length) : $contact_number_n,
                            'userid' => Auth::guard('web')->user()->id,
                            'remarks' => $campaing,
                            'number_of_sms' => $this->messagecount,
                            'sms_catagory' => $senderidtype,
                            'sms_type' => $this->messagetype,
                            'sms_content' => $contact_number['message'],
                            'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                            'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                            //'user_sent_sms_id' => $sent_sms->id
                        ];


                        $smssentdata[] = [
                            'remarks' => $campaing,
                            'user_id' => Auth::guard('web')->user()->id,
                            'user_sender_id' => $clientsenderids->id,
                            'to_number' => ($shortoperator == 'gp' || $shortoperator == 'gpx') && $contact_length == 13 ? Str::substr($contact_number_n,2,$contact_length) : $contact_number_n,//$contact_number_n,
                            'sms_type' => $this->messagetype,
                            'sms_catagory' => $senderidtype,
                            'sms_content' => $contact_number['message'],
                            'number_of_sms' => $this->messagecount,
                            'total_contacts' => 1,
                            'send_type' => 'smsadmin',
                            'contact_group_id' => 0,
                            'status' => false,
                            'submitted_at' => Carbon::now(),
                        ];

                    }
            } else {
                            
                    if (in_array($opt, $validprefix))
                    {
                        if ($opt == "88017") {
                            $shortoperator = 'gp';
                        }

                        if ($opt == "88013") {
                            $shortoperator = 'gpx';
                        }

                        if ($opt == "88019") {
                            $shortoperator = 'blink';
                        }

                        if ($opt == "88014") {
                            $shortoperator = 'blx';
                        }

                        if ($opt == "88015") {
                            $shortoperator = 'teletalk';
                        }

                        if ($opt == "88018") {
                            $shortoperator = 'robi';
                        }

                        if ($opt == "88016") {
                            $shortoperator = 'airtel';
                        }

                        if ($opt == "88099") {
                            $shortoperator = 'rankstel';
                        }


                        $operator_contacts[$shortoperator][] = [
                            'contact' => ($shortoperator == 'gp' || $shortoperator == 'gpx') && $contact_length == 13 ? Str::substr($contact_number_n,2,$contact_length) : $contact_number_n,
                            'userid' => Auth::guard('web')->user()->id,
                            'remarks' => $campaing,
                            'number_of_sms' => $this->messagecount,
                            'sms_catagory' => $senderidtype,
                            'sms_type' => $this->messagetype,
                            'sms_content' => $contact_number['message'],
                            'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                            'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                            //'user_sent_sms_id' => $sent_sms->id
                        ];

                        $smssentdata[] = [
                            'remarks' => $campaing,
                            'user_id' => Auth::guard('web')->user()->id,
                            'user_sender_id' => $clientsenderids->id,
                            'to_number' => ($shortoperator == 'gp' || $shortoperator == 'gpx') && $contact_length == 13 ? Str::substr($contact_number_n,2,$contact_length) : $contact_number_n,//$contact_number_n,
                            'sms_type' => $this->messagetype,
                            'sms_catagory' => $senderidtype,
                            'sms_content' => $contact_number['message'],
                            'number_of_sms' => $this->messagecount,
                            'total_contacts' => 1,
                            'send_type' => 'smsadmin',
                            'contact_group_id' => 0,
                            'status' => false,
                            'submitted_at' => Carbon::now(),
                        ];
                    }

            }
        }

        foreach (array_chunk($smssentdata,5000) as $t)  
        {
            $backupstage = DB::table("backups_stage")->get();

            if ($backupstage[0]->status) {
                DB::table('user_sent_smses_backup')->insert($t); 
            } else {
                DB::table('user_sent_smses')->insert($t); 
            }
        }

        if (! CheckRuntimeBalance::where('userid',Auth::guard('web')->user()->id)
                                    ->exists()
        ) {

            CheckRuntimeBalance::create([
                'userid' => Auth::guard('web')->user()->id,
                'ischecked' => false
            ]);
        }

        foreach(array_keys($operator_contacts) as $operator)
        {
            
            if ($clientsenderids->gateway_info == NULL)
            {   
                foreach($operator_contacts[$operator] as $opcontact)
                {
                    $data = [ 
                        "userid"        => $opcontact['userid'],
                        "campaing"  => $opcontact['remarks'],
                        "senderidtype" => $opcontact['sms_catagory'],
                        "user"		=> $gateways->user,                //$optUrlUser[$optID],               
                        "pass"		=> $gateways->password,                //$optUrlPass[$optID],  
                        "op"		=> "SMS", 
                        "mobile"	    => $opcontact['contact'],
                        "messagecount" => $opcontact['number_of_sms'],  
                        "cli"	  	    => $request->senderid,     
                        "charset"	=> $opcontact['sms_type'], 
                        "sms"		=> $opcontact['sms_content'],
                        'owner_id' => $opcontact['owner_id'],
                        'owner_type' => $opcontact['owner_type'],
                        'post_url' =>  'http://bulksms.teletalk.com.bd/link_sms_send.php?op=SMS',
                        
                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                    ];

                    $this->smssend->smsSendToTelitalk($data);
                }
                
            } else {
            
                switch($operator)
                {
                    case 'teletalk':
                        //Teletalk
                        $gateway = collect($gateways)->where('sender_operator_name','Teletalk');

                        foreach($gateway as $gate)
                        {
                            $teletalkassociate_sender_id = $gate->associate_sender_id;
                            $associate_gateway = Gateway::where('id',$gate->associate_gateway)->first();
                        }

                        if ($associate_gateway->id == 1)
                        {   //teletalk
                            $data = [];
                            foreach($operator_contacts[$operator] as $opcontact)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"  => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "user"		=> $associate_gateway->user,                //$optUrlUser[$optID],               
                                    "pass"		=> $associate_gateway->password,                //$optUrlPass[$optID],  
                                    "op"		=> "SMS", 
                                    "mobile"	    => $opcontact['contact'],
                                    "messagecount" => $opcontact['number_of_sms'],  
                                    "cli"	  	    => str_replace('\"'," ",$teletalkassociate_sender_id),//$request->senderid,     
                                    "charset"	=> $opcontact['sms_type'], 
                                    "sms"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url' =>  $associate_gateway->api_url,
                                    
                                    //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];
                                
                                $this->smssend->smsSendToTelitalk($data);
                            }
                        } else if($associate_gateway->id == 2) {
                            //gp
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['teletalk'][0]['userid'],
                                    "campaing"      => $operator_contacts['teletalk'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['teletalk'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['teletalk'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['teletalk'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 3) {
                            //blink
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['teletalk'][0]['userid'],
                                    'campaing' => $operator_contacts['teletalk'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['teletalk'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['teletalk'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBlink($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBlink($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        str_replace('\"'," ",$teletalkassociate_sender_id),//'sender'   => $clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 4) {
                            //robi
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['teletalk'][0]['userid'],
                                    'campaing' => $operator_contacts['teletalk'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['teletalk'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['teletalk'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 5) {
                            //airtel
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['teletalk'][0]['userid'],
                                    'campaing' => $operator_contacts['teletalk'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['teletalk'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['teletalk'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 6) {
                            //blx
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['teletalk'][0]['userid'],
                                    'campaing' => $operator_contacts['teletalk'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['teletalk'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['teletalk'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBlink($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBlink($data);
                                        unset($gp100);
                                    }
                                    if ($i< 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 7) {
                            //gpx
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['teletalk'][0]['userid'],
                                    "campaing"      => $operator_contacts['teletalk'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['teletalk'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['teletalk'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['teletalk'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 8) {
                            //rankstel
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['teletalk'][0]['userid'],
                                    'campaing' => $operator_contacts['teletalk'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['teletalk'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['teletalk'][0]['number_of_sms'],
                                    "messagetype"	=> $operator_contacts['teletalk'][0]['sms_type'],
                                    'sender'   => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRanksTel($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            "messagetype"	=> $opcontact['sms_type'],
                                            'sender'   => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToRanksTel($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        "messagetype"	=> $opcontact['sms_type'],
                                        'sender'   => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRanksTel($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 9) {
                            //gp-3
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['teletalk'][0]['userid'],
                                    "campaing"      => $operator_contacts['teletalk'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['teletalk'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['teletalk'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['teletalk'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 10) {
                            //gp-2
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['teletalk'][0]['userid'],
                                    "campaing"      => $operator_contacts['teletalk'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['teletalk'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['teletalk'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['teletalk'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 15) {
                            //EasyWeb
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['teletalk'][0]['userid'],
                                    'campaing' => $operator_contacts['teletalk'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['teletalk'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['teletalk'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToEasyWeb($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToEasyWeb($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToEasyWeb($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 16) {
                            //Banlaphone
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['teletalk'][0]['userid'],
                                    'campaing' => $operator_contacts['teletalk'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['teletalk'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'username' => $associate_gateway->user,
                                    'password' => $associate_gateway->password,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['teletalk'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBanglaPhone($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'username' => $associate_gateway->user,
                                            'password' => $associate_gateway->password,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBanglaPhone($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'username' => $associate_gateway->user,
                                        'password' => $associate_gateway->password,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBanglaPhone($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 18) {
                            //robi multi message
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['teletalk'][0]['userid'],
                                    'campaing' => $operator_contacts['teletalk'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['teletalk'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['teletalk'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$teletalkassociate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        }
                        break;
                    case 'gp':
                        //gp
                        $gateway = collect($gateways)->where('sender_operator_name','GP');
                        foreach($gateway as $gate)
                        {
                            $gpxassociate_sender_id = $gate->associate_sender_id;
                            $associate_gateway = Gateway::where('id',$gate->associate_gateway)->first();
                        }
                        
                        if ($associate_gateway->id == 1)
                        {   //teletalk
                            $data = [];
                            foreach($operator_contacts[$operator] as $opcontact)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"  => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "user"		=> $associate_gateway->user,                //$optUrlUser[$optID],               
                                    "pass"		=> $associate_gateway->password,                //$optUrlPass[$optID],  
                                    "op"		=> "SMS", 
                                    "mobile"	    => $opcontact['contact'],
                                    "messagecount" => $opcontact['number_of_sms'],  
                                    "cli"	  	    => str_replace('\"'," ",$gpxassociate_sender_id),//$request->senderid,     
                                    "charset"	=> $opcontact['sms_type'], 
                                    "sms"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url' =>  $associate_gateway->api_url,
                                    
                                    //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToTelitalk($data);
                            }
                        } else if($associate_gateway->id == 2) {
                            //gp
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['gp'][0]['userid'],
                                    "campaing"      => $operator_contacts['gp'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['gp'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['gp'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['gp'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 3) {
                            //blink
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['gp'][0]['userid'],
                                    'campaing' => $operator_contacts['gp'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['gp'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['gp'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    
                                ];
                                $this->smssend->smsSendToBlink($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBlink($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 4) {
                            //robi
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['gp'][0]['userid'],
                                    'campaing' => $operator_contacts['gp'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['gp'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['gp'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        $i = 0;
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 5) {
                            //airtel
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['gp'][0]['userid'],
                                    'campaing' => $operator_contacts['gp'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['gp'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['gp'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        $i = 0;
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 6) {
                            //blx
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['gp'][0]['userid'],
                                    'campaing' => $operator_contacts['gp'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['gp'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['gp'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    
                                ];
                                $this->smssend->smsSendToBlink($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBlink($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 7) {
                            //gpx
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['gp'][0]['userid'],
                                    "campaing"      => $operator_contacts['gp'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['gp'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['gp'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['gp'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    
                                    //'user_sent_sms_id' => $operator_contacts['gp'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 8) {
                            //rankstel
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['gp'][0]['userid'],
                                    'campaing' => $operator_contacts['gp'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['gp'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['gp'][0]['number_of_sms'],
                                    "messagetype"	=> $operator_contacts['gp'][0]['sms_type'],
                                    'sender'   => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['gp'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRanksTel($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            "messagetype"	=> $opcontact['sms_type'],
                                            'sender'   => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToRanksTel($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        "messagetype"	=> $opcontact['sms_type'],
                                        'sender'   => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRanksTel($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 9) {
                            //gp-3
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['gp'][0]['userid'],
                                    "campaing"      => $operator_contacts['gp'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['gp'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['gp'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['gp'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    
                                    //'user_sent_sms_id' => $operator_contacts['gp'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 10) {
                            //gp-2
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['gp'][0]['userid'],
                                    "campaing"      => $operator_contacts['gp'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['gp'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['gp'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['gp'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['gp'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 15) {
                            //EasyWeb
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['gp'][0]['userid'],
                                    'campaing' => $operator_contacts['gp'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['gp'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['gp'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['gp'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToEasyWeb($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToEasyWeb($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToEasyWeb($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 16) {
                            //Banlaphone
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['gp'][0]['userid'],
                                    'campaing' => $operator_contacts['gp'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['gp'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'username' => $associate_gateway->user,
                                    'password' => $associate_gateway->password,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['gp'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBanglaPhone($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'username' => $associate_gateway->user,
                                            'password' => $associate_gateway->password,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBanglaPhone($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'username' => $associate_gateway->user,
                                        'password' => $associate_gateway->password,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBanglaPhone($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 18) {
                            //robi
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['gp'][0]['userid'],
                                    'campaing' => $operator_contacts['gp'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['gp'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['gp'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$gpxassociate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        }
                    break;
                    case 'gpx':
                        $gateway = collect($gateways)->where('sender_operator_name','GPx');

                        foreach($gateway as $gate)
                        {
                            $associate_sender_id = $gate->associate_sender_id;
                            $associate_gateway = Gateway::where('id',$gate->associate_gateway)->first();
                        }
                        

                        if ($associate_gateway->id == 1)
                        {   //teletalk
                            $data = [];
                            foreach($operator_contacts[$operator] as $opcontact)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"  => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "user"		=> $associate_gateway->user,                //$optUrlUser[$optID],               
                                    "pass"		=> $associate_gateway->password,                //$optUrlPass[$optID],  
                                    "op"		=> "SMS", 
                                    "mobile"	    => $opcontact['contact'],
                                    "messagecount" => $opcontact['number_of_sms'],  
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$request->senderid,     
                                    "charset"	=> $opcontact['sms_type'], 
                                    "sms"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url' =>  $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToTelitalk($data);
                            }
                        } else if($associate_gateway->id == 2) {
                            //gp
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['gpx'][0]['userid'],
                                    "campaing"      => $operator_contacts['gpx'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['gpx'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['gpx'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['gpx'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 3) {
                            //blink
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['gpx'][0]['userid'],
                                    'campaing' => $operator_contacts['gpx'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['gpx'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['gpx'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBlink($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBlink($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 4) {
                            //robi
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['gpx'][0]['userid'],
                                    'campaing' => $operator_contacts['gpx'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['gpx'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['gpx'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 5) {
                            //airtel
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['gpx'][0]['userid'],
                                    'campaing' => $operator_contacts['gpx'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['gpx'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['gpx'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 6) {
                            //blx
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['gpx'][0]['userid'],
                                    'campaing' => $operator_contacts['gpx'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['gpx'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['gpx'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBlink($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBlink($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 7) {
                            //gpx
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['gpx'][0]['userid'],
                                    "campaing"      => $operator_contacts['gpx'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['gpx'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['gpx'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['gpx'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 8) {
                            //rankstel
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['gpx'][0]['userid'],
                                    'campaing' => $operator_contacts['gpx'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['gpx'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['gpx'][0]['number_of_sms'],
                                    "messagetype"	=> $operator_contacts['gpx'][0]['sms_type'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRanksTel($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            "messagetype"	=> $opcontact['sms_type'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToRanksTel($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        "messagetype"	=> $opcontact['sms_type'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRanksTel($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 9) {
                            //gp-3
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['gpx'][0]['userid'],
                                    "campaing"      => $operator_contacts['gpx'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['gpx'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['gpx'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['gpx'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 10) {
                            //gp-2
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['gpx'][0]['userid'],
                                    "campaing"      => $operator_contacts['gpx'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['gpx'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['gpx'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['gpx'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 15) {
                            //EasyWeb
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['gpx'][0]['userid'],
                                    'campaing' => $operator_contacts['gpx'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['gpx'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['gpx'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToEasyWeb($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToEasyWeb($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToEasyWeb($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 16) {
                            //Banlaphone
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['gpx'][0]['userid'],
                                    'campaing' => $operator_contacts['gpx'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['gpx'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'username' => $associate_gateway->user,
                                    'password' => $associate_gateway->password,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['gpx'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBanglaPhone($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'username' => $associate_gateway->user,
                                            'password' => $associate_gateway->password,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBanglaPhone($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'username' => $associate_gateway->user,
                                        'password' => $associate_gateway->password,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBanglaPhone($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 18) {
                            //robi
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['gpx'][0]['userid'],
                                    'campaing' => $operator_contacts['gpx'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['gpx'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['gpx'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        }
                    break;
                    case 'blink':
                        $gateway = collect($gateways)->where('sender_operator_name','Banglalink');

                        foreach($gateway as $gate)
                        {
                            $associate_sender_id = $gate->associate_sender_id;
                            $associate_gateway = Gateway::where('id',$gate->associate_gateway)->first();
                        }
                        

                        if ($associate_gateway->id == 1)
                        {   //teletalk
                            $data = [];
                            foreach($operator_contacts[$operator] as $opcontact)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"  => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "user"		=> $associate_gateway->user,                //$optUrlUser[$optID],               
                                    "pass"		=> $associate_gateway->password,                //$optUrlPass[$optID],  
                                    "op"		=> "SMS", 
                                    "mobile"	    => $opcontact['contact'],
                                    "messagecount" => $opcontact['number_of_sms'],  
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$request->senderid,     
                                    "charset"	=> $opcontact['sms_type'], 
                                    "sms"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url' =>  $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToTelitalk($data);
                            }
                        } else if($associate_gateway->id == 2) {
                            //gp
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['blink'][0]['userid'],
                                    "campaing"      => $operator_contacts['blink'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['blink'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['blink'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['blink'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 3) {
                            //blink
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['blink'][0]['userid'],
                                    'campaing' => $operator_contacts['blink'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['blink'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['blink'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBlink($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBlink($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 4) {
                            //robi
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['blink'][0]['userid'],
                                    'campaing' => $operator_contacts['blink'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['blink'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['blink'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 5) {
                            //airtel
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['blink'][0]['userid'],
                                    'campaing' => $operator_contacts['blink'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['blink'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['blink'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 6) {
                            //blx
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['blink'][0]['userid'],
                                    'campaing' => $operator_contacts['blink'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['blink'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['blink'][0]['number_of_sms'],
                                    'sender'   =>str_replace('\"'," ",$associate_sender_id),// $clientsenderids->sender_name,
                                    'message'  => $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBlink($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBlink($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 7) {
                            //gpx
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['blink'][0]['userid'],
                                    "campaing"      => $operator_contacts['blink'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['blink'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['blink'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['blink'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 8) {
                            //rankstel
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['blink'][0]['userid'],
                                    'campaing' => $operator_contacts['blink'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['blink'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['blink'][0]['number_of_sms'],
                                    "messagetype"	=> $operator_contacts['blink'][0]['sms_type'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRanksTel($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            "messagetype"	=> $opcontact['sms_type'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToRanksTel($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        "messagetype"	=> $opcontact['sms_type'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRanksTel($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 9) {
                            //gp-3
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['blink'][0]['userid'],
                                    "campaing"      => $operator_contacts['blink'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['blink'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['blink'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['blink'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 10) {
                            //gp-2
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['blink'][0]['userid'],
                                    "campaing"      => $operator_contacts['blink'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['blink'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['blink'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['blink'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 15) {
                            //EasyWeb
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['blink'][0]['userid'],
                                    'campaing' => $operator_contacts['blink'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['blink'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['blink'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToEasyWeb($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToEasyWeb($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToEasyWeb($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 16) {
                            //Banlaphone
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['blink'][0]['userid'],
                                    'campaing' => $operator_contacts['blink'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['blink'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'username' => $associate_gateway->user,
                                    'password' => $associate_gateway->password,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['blink'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBanglaPhone($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'username' => $associate_gateway->user,
                                            'password' => $associate_gateway->password,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBanglaPhone($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'username' => $associate_gateway->user,
                                        'password' => $associate_gateway->password,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBanglaPhone($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 18) {
                            //robi
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['blink'][0]['userid'],
                                    'campaing' => $operator_contacts['blink'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['blink'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['blink'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        }
                    break;
                    case 'blx':
                        
                        $gateway = collect($gateways)->where('sender_operator_name','BLx');

                        foreach($gateway as $gate)
                        {
                            $associate_sender_id = $gate->associate_sender_id;
                            $associate_gateway = Gateway::where('id',$gate->associate_gateway)->first();
                        }
                        

                        if ($associate_gateway->id == 1)
                        {   //teletalk
                            $data = [];
                            foreach($operator_contacts[$operator] as $opcontact)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"  => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "user"		=> $associate_gateway->user,                //$optUrlUser[$optID],               
                                    "pass"		=> $associate_gateway->password,                //$optUrlPass[$optID],  
                                    "op"		=> "SMS", 
                                    "mobile"	    => $opcontact['contact'],
                                    "messagecount" => $opcontact['number_of_sms'],  
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$request->senderid,    
                                    "charset"	=> $opcontact['sms_type'], 
                                    "sms"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url' =>  $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToTelitalk($data);
                            }
                        } else if($associate_gateway->id == 2) {
                            //gp
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['blx'][0]['userid'],
                                    "campaing"      => $operator_contacts['blx'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['blx'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['blx'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['blx'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 3) {
                            //blink
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['blx'][0]['userid'],
                                    'campaing' => $operator_contacts['blx'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['blx'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['blx'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBlink($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBlink($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 4) {
                            //robi
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['blx'][0]['userid'],
                                    'campaing' => $operator_contacts['blx'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['blx'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['blx'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 5) {
                            //airtel
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['blx'][0]['userid'],
                                    'campaing' => $operator_contacts['blx'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['blx'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['blx'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 6) {
                            //blx
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['blx'][0]['userid'],
                                    'campaing' => $operator_contacts['blx'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['blx'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['blx'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBlink($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBlink($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 7) {
                            //gpx
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['blx'][0]['userid'],
                                    "campaing"      => $operator_contacts['blx'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['blx'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['blx'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['blx'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 8) {
                            //rankstel
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['blx'][0]['userid'],
                                    'campaing' => $operator_contacts['blx'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['blx'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['blx'][0]['number_of_sms'],
                                    "messagetype"	=> $operator_contacts['blx'][0]['sms_type'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRanksTel($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            "messagetype"	=> $opcontact['sms_type'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToRanksTel($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        "messagetype"	=> $opcontact['sms_type'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRanksTel($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 9) {
                            //gp-3
                            $gp100 = [];
                            $i = 0;
                            $j= 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['blx'][0]['userid'],
                                    "campaing"      => $operator_contacts['blx'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['blx'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['blx'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['blx'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 10) {
                            //gp-2
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['blx'][0]['userid'],
                                    "campaing"      => $operator_contacts['blx'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['blx'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['blx'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['blx'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 15) {
                            //EasyWeb
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['blx'][0]['userid'],
                                    'campaing' => $operator_contacts['blx'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['blx'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['blx'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToEasyWeb($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToEasyWeb($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToEasyWeb($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 16) {
                            //Banlaphone
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['blx'][0]['userid'],
                                    'campaing' => $operator_contacts['blx'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['blx'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'username' => $associate_gateway->user,
                                    'password' => $associate_gateway->password,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['blx'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBanglaPhone($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'username' => $associate_gateway->user,
                                            'password' => $associate_gateway->password,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBanglaPhone($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'username' => $associate_gateway->user,
                                        'password' => $associate_gateway->password,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBanglaPhone($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 18) {
                            //robi multi message
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['blx'][0]['userid'],
                                    'campaing' => $operator_contacts['blx'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['blx'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['blx'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        }
                    break;
                    case 'robi':
                        
                        $gateway = collect($gateways)->where('sender_operator_name','Robi');

                        foreach($gateway as $gate)
                        {
                            $associate_sender_id = $gate->associate_sender_id;

                            $associate_gateway = Gateway::where('id',$gate->associate_gateway)->first();
                        }
                        

                        if ($associate_gateway->id == 1)
                        {   //teletalk
                            $data = [];
                            foreach($operator_contacts[$operator] as $opcontact)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"  => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "user"		=> $associate_gateway->user,                //$optUrlUser[$optID],               
                                    "pass"		=> $associate_gateway->password,                //$optUrlPass[$optID],  
                                    "op"		=> "SMS", 
                                    "mobile"	    => $opcontact['contact'],
                                    "messagecount" => $opcontact['number_of_sms'],  
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$request->senderid,    
                                    "charset"	=> $opcontact['sms_type'], 
                                    "sms"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url' =>  $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToTelitalk($data);
                            }
                        } else if($associate_gateway->id == 2) {
                            //gp
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['robi'][0]['userid'],
                                    "campaing"      => $operator_contacts['robi'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['robi'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['robi'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['robi'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 3) {
                            //blink
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['robi'][0]['userid'],
                                    'campaing' => $operator_contacts['robi'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['robi'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['robi'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBlink($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBlink($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 4) {
                            //robi
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['robi'][0]['userid'],
                                    'campaing' => $operator_contacts['robi'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['robi'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['robi'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 5) {
                            //airtel
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['robi'][0]['userid'],
                                    'campaing' => $operator_contacts['robi'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['robi'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['robi'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 6) {
                            //blx
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['robi'][0]['userid'],
                                    'campaing' => $operator_contacts['robi'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['robi'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['robi'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBlink($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBlink($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 7) {
                            //gpx
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['robi'][0]['userid'],
                                    "campaing"      => $operator_contacts['robi'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['robi'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['robi'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['robi'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 8) {
                            //rankstel
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['robi'][0]['userid'],
                                    'campaing' => $operator_contacts['robi'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['robi'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['robi'][0]['number_of_sms'],
                                    "messagetype"	=> $operator_contacts['robi'][0]['sms_type'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRanksTel($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            "messagetype"	=> $opcontact['sms_type'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToRanksTel($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        "messagetype"	=> $opcontact['sms_type'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRanksTel($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 9) {
                            //gp-3
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['robi'][0]['userid'],
                                    "campaing"      => $operator_contacts['robi'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['robi'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['robi'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['robi'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 10) {
                            //gp-2
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['robi'][0]['userid'],
                                    "campaing"      => $operator_contacts['robi'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['robi'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['robi'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['robi'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 15) {
                            //EasyWeb
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['robi'][0]['userid'],
                                    'campaing' => $operator_contacts['robi'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['robi'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['robi'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToEasyWeb($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToEasyWeb($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToEasyWeb($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 16) {
                            //Banlaphone
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['robi'][0]['userid'],
                                    'campaing' => $operator_contacts['robi'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['robi'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'username' => $associate_gateway->user,
                                    'password' => $associate_gateway->password,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['robi'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBanglaPhone($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'username' => $associate_gateway->user,
                                            'password' => $associate_gateway->password,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBanglaPhone($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'username' => $associate_gateway->user,
                                        'password' => $associate_gateway->password,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBanglaPhone($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 18) {
                            //robi multi message
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['robi'][0]['userid'],
                                    'campaing' => $operator_contacts['robi'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['robi'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['robi'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        }
                    break;
                    case 'airtel':
                        
                        $gateway = collect($gateways)->where('sender_operator_name','Airtel');

                        foreach($gateway as $gate)
                        {
                            $associate_sender_id = $gate->associate_sender_id;
                            $associate_gateway = Gateway::where('id',$gate->associate_gateway)->first();
                        }
                        

                        if ($associate_gateway->id == 1)
                        {   //teletalk
                            $data = [];
                            foreach($operator_contacts[$operator] as $opcontact)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"  => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "user"		=> $associate_gateway->user,                //$optUrlUser[$optID],               
                                    "pass"		=> $associate_gateway->password,                //$optUrlPass[$optID],  
                                    "op"		=> "SMS", 
                                    "mobile"	    => $opcontact['contact'],
                                    "messagecount" => $opcontact['number_of_sms'],  
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$request->senderid,     
                                    "charset"	=> $opcontact['sms_type'], 
                                    "sms"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url' =>  $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToTelitalk($data);
                            }
                        } else if($associate_gateway->id == 2) {
                            //gp
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['airtel'][0]['userid'],
                                    "campaing"      => $operator_contacts['airtel'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['airtel'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['airtel'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['airtel'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 3) {
                            //blink
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['airtel'][0]['userid'],
                                    'campaing' => $operator_contacts['airtel'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['airtel'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['airtel'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBlink($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBlink($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 4) {
                            //robi
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['airtel'][0]['userid'],
                                    'campaing' => $operator_contacts['airtel'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['airtel'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['airtel'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i  = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 5) {
                            //airtel
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['airtel'][0]['userid'],
                                    'campaing' => $operator_contacts['airtel'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['airtel'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['airtel'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 6) {
                            //blx
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['airtel'][0]['userid'],
                                    'campaing' => $operator_contacts['airtel'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['airtel'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['airtel'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBlink($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBlink($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 7) {
                            //gpx
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['airtel'][0]['userid'],
                                    "campaing"      => $operator_contacts['airtel'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['airtel'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['airtel'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['airtel'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 8) {
                            //rankstel
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['airtel'][0]['userid'],
                                    'campaing' => $operator_contacts['airtel'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['airtel'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['airtel'][0]['number_of_sms'],
                                    "messagetype"	=> $operator_contacts['airtel'][0]['sms_type'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRanksTel($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            "messagetype"	=> $opcontact['sms_type'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToRanksTel($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        "messagetype"	=> $opcontact['sms_type'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRanksTel($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 9) {
                            //gp-3
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['airtel'][0]['userid'],
                                    "campaing"      => $operator_contacts['airtel'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['airtel'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['airtel'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['airtel'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 10) {
                            //gp-2
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['airtel'][0]['userid'],
                                    "campaing"      => $operator_contacts['airtel'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['airtel'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['airtel'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['airtel'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 15) {
                            //EasyWeb
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['airtel'][0]['userid'],
                                    'campaing' => $operator_contacts['airtel'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['airtel'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['airtel'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToEasyWeb($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToEasyWeb($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToEasyWeb($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 16) {
                            //Banlaphone
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['airtel'][0]['userid'],
                                    'campaing' => $operator_contacts['airtel'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['airtel'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'username' => $associate_gateway->user,
                                    'password' => $associate_gateway->password,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['airtel'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBanglaPhone($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'username' => $associate_gateway->user,
                                            'password' => $associate_gateway->password,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBanglaPhone($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'username' => $associate_gateway->user,
                                        'password' => $associate_gateway->password,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBanglaPhone($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 18) {
                            //robi multi message
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['airtel'][0]['userid'],
                                    'campaing' => $operator_contacts['airtel'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['airtel'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['airtel'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        }
                    break;
                    case 'rankstel':
                        
                        $gateway = collect($gateways)->where('sender_operator_name','RanksTell');

                        foreach($gateway as $gate)
                        {
                            $associate_sender_id = $gate->associate_sender_id;
                            $associate_gateway = Gateway::where('id',$gate->associate_gateway)->first();
                        }
                        

                        if ($associate_gateway->id == 1)
                        {   //teletalk
                            $data = [];
                            foreach($operator_contacts[$operator] as $opcontact)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"  => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "user"		=> $associate_gateway->user,                //$optUrlUser[$optID],               
                                    "pass"		=> $associate_gateway->password,                //$optUrlPass[$optID],  
                                    "op"		=> "SMS", 
                                    "mobile"	    => $opcontact['contact'],
                                    "messagecount" => $opcontact['number_of_sms'],  
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$request->senderid,     
                                    "charset"	=> $opcontact['sms_type'], 
                                    "sms"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url' =>  $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToTelitalk($data);
                            }
                        } else if($associate_gateway->id == 2) {
                            //gp
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['rankstel'][0]['userid'],
                                    "campaing"      => $operator_contacts['rankstel'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['rankstel'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['rankstel'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['rankstel'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['rankstel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['rankstel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['rankstel'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['rankstel'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 3) {
                            //blink
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['rankstel'][0]['userid'],
                                    'campaing' => $operator_contacts['rankstel'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['rankstel'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['rankstel'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['rankstel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['rankstel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['rankstel'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['rankstel'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBlink($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBlink($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 4) {
                            //robi
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['rankstel'][0]['userid'],
                                    'campaing' => $operator_contacts['rankstel'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['rankstel'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['rankstel'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['rankstel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['rankstel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['rankstel'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['rankstel'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i  = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 5) {
                            //airtel
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['rankstel'][0]['userid'],
                                    'campaing' => $operator_contacts['rankstel'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['rankstel'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['rankstel'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['rankstel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['rankstel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['rankstel'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['rankstel'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 6) {
                            //blx
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['rankstel'][0]['userid'],
                                    'campaing' => $operator_contacts['rankstel'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['rankstel'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['rankstel'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['rankstel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['rankstel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['rankstel'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['rankstel'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBlink($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBlink($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 7) {
                            //gpx
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['rankstel'][0]['userid'],
                                    "campaing"      => $operator_contacts['rankstel'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['rankstel'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['rankstel'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['rankstel'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['rankstel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['rankstel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['rankstel'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['rankstel'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 8) {
                            //rankstel
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['rankstel'][0]['userid'],
                                    'campaing' => $operator_contacts['rankstel'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['rankstel'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['rankstel'][0]['number_of_sms'],
                                    "messagetype"	=> $operator_contacts['rankstel'][0]['sms_type'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['rankstel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['rankstel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['rankstel'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['rankstel'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRanksTel($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            "messagetype"	=> $opcontact['sms_type'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToRanksTel($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        "messagetype"	=> $opcontact['sms_type'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRanksTel($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 9) {
                            //gp-3
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['rankstel'][0]['userid'],
                                    "campaing"      => $operator_contacts['rankstel'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['rankstel'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['rankstel'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['rankstel'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['rankstel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['rankstel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['rankstel'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['rankstel'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 10) {
                            //gp-2
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {

                                    $gp100[] = $opcontact['contact'];
                                    
                                }

                                $data = [ 
                                    "userid"        => $operator_contacts['rankstel'][0]['userid'],
                                    "campaing"      => $operator_contacts['rankstel'][0]['remarks'],
                                    "senderidtype" => $operator_contacts['rankstel'][0]['sms_catagory'],
                                    "username"		=> $associate_gateway->user,               
                                    "password"		=> $associate_gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $operator_contacts['rankstel'][0]['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['rankstel'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['rankstel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['rankstel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['rankstel'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    //'user_sent_sms_id' => $operator_contacts['rankstel'][0]['user_sent_sms_id']
                                    
                                ];
                                $this->smssend->smsSendToGp($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [ 
                                            "userid"        => $opcontact['userid'],
                                            "campaing"      => $opcontact['remarks'],
                                            "senderidtype" => $opcontact['sms_catagory'],
                                            "username"		=> $associate_gateway->user,               
                                            "password"		=> $associate_gateway->password,  
                                            "apicode"		=> "1", 
                                            "msisdn"	    => implode(",",$gp100),
                                            "messagecount" => $opcontact['number_of_sms'],
                                            "countrycode"	=> "880",
                                            "messageid"		=> 0,
                                            "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToGp($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [ 
                                        "userid"        => $opcontact['userid'],
                                        "campaing"      => $opcontact['remarks'],
                                        "senderidtype" => $opcontact['sms_catagory'],
                                        "username"		=> $associate_gateway->user,               
                                        "password"		=> $associate_gateway->password,  
                                        "apicode"		=> "1", 
                                        "msisdn"	    => implode(",",$gp100),
                                        "messagecount" => $opcontact['number_of_sms'],
                                        "countrycode"	=> "880",
                                        "messageid"		=> 0,
                                        "cli"	  	    => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 15) {
                            //EasyWeb
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['rankstel'][0]['userid'],
                                    'campaing' => $operator_contacts['rankstel'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['rankstel'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['rankstel'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['rankstel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['rankstel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['rankstel'][0]['owner_type'],
                                    //'user_sent_sms_id' => $operator_contacts['rankstel'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToEasyWeb($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToEasyWeb($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToEasyWeb($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 16) {
                            //Banlaphone
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 100)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }

                                $data = [
                                    'userid'   => $operator_contacts['rankstel'][0]['userid'],
                                    'campaing' => $operator_contacts['rankstel'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['rankstel'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'username' => $associate_gateway->user,
                                    'password' => $associate_gateway->password,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['rankstel'][0]['number_of_sms'],
                                    'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'message'  => $operator_contacts['rankstel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['rankstel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['rankstel'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToBanglaPhone($data);
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 100)
                                    {
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'username' => $associate_gateway->user,
                                            'password' => $associate_gateway->password,
                                            'msisdn'   => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        $i = 0;
                                        $this->smssend->smsSendToBanglaPhone($data);
                                        unset($gp100);
                                    }
                                    if ($i < 100 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'username' => $associate_gateway->user,
                                        'password' => $associate_gateway->password,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBanglaPhone($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 18) {
                            //robi multi message
                            $gp100 = [];
                            $i = 0;
                            $j = 0;
                            $data = [];
                            if (count($operator_contacts[$operator]) < 200)
                            {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    $gp100[] = $opcontact['contact'];
                                }
                                $data = [
                                    'userid'   => $operator_contacts['rankstel'][0]['userid'],
                                    'campaing' => $operator_contacts['rankstel'][0]['remarks'],
                                    'senderidtype' => $operator_contacts['rankstel'][0]['sms_catagory'],
                                    'post_url' => $associate_gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $operator_contacts['rankstel'][0]['number_of_sms'],
                                    'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['rankstel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['rankstel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['rankstel'][0]['owner_type'],
                                    
                                    //'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                if ($senderidtype == 'nomask')
                                {
                                    $this->smssend->smsSendToRobiNonMask($data);
                                } else {
                                    $this->smssend->smsSendToRobi($data);
                                }
                            } else {
                                foreach($operator_contacts[$operator] as $opcontact)
                                {
                                    
                                    if (count($gp100) == 200)
                                    {
                                        
                                        $data = [
                                            'userid'   => $opcontact['userid'],
                                            'campaing' => $opcontact['remarks'],
                                            'senderidtype' => $opcontact['sms_catagory'],
                                            'post_url' => $associate_gateway->api_url,
                                            'To'       => implode(",",$gp100),
                                            'messagecount' => $opcontact['number_of_sms'],
                                            'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            
                                            //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        if ($senderidtype == 'nomask')
                                        {
                                            $this->smssend->smsSendToRobiNonMask($data);
                                        } else {
                                            $this->smssend->smsSendToRobi($data);
                                        }
                                        $i = 0;
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i < 200 && $j < count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        $j++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => str_replace('\"'," ",$associate_sender_id),//$clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        
                                        //'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    if ($senderidtype == 'nomask')
                                    {
                                        $this->smssend->smsSendToRobiNonMask($data);
                                    } else {
                                        $this->smssend->smsSendToRobi($data);
                                    }
                                }
                            }
                            //return $data;
                        }
                    break;
                    default:
                    break;
                }
            }

            
        }
        
        
        $clientmasksmsbal_1 = $this->product->getSmsBalanceByCategory($clientid->id,'mask');
        $clientnonmasksmsbal_1 = $this->product->getSmsBalanceByCategory($clientid->id,'nomask');
        $clientvoicemsbal_1 = $this->product->getSmsBalanceByCategory($clientid->id,'voice');

        $totalmaskbal_1 = ($clientmasksmsbal_1-$this->consumesms->totalConsumeMaskBalance($clientid->id));
        $totalnonmaskbal_1 = ($clientnonmasksmsbal_1-$this->consumesms->totalConsumeNonMaskBalance($clientid->id));
        $totalvoicebal_1 = ($clientvoicemsbal_1-$this->consumesms->totalConsumeVoiceBalance($clientid->id));

        session()->put('totalmaskbal', $totalmaskbal_1);
        session()->put('totalnonmaskbal', $totalnonmaskbal_1);
        session()->put('totalvoicebal', $totalvoicebal_1);

        $camid = substr($campaing,9, strlen($campaing));

        if (session()->has('senderr'))
        {
            return response()->json(['errmsg' => session()->get('senderr')],406);
        }

        if ($totalmaskbal_1 <= $totalmaskbal || $totalnonmaskbal_1 <= $totalnonmaskbal || $totalvoicebal_1<= $totalvoicebal)
        {
            if ($senderidtype == 'mask')
            {
                if (session()->has('sendsuccess'))
                {
                    return response()->json(['msg' => session()->get('sendsuccess'),'mask' => $totalmaskbal_1,'MessageId' => $camid],200);
                }

                if (session()->has('senderr'))
                {
                    return response()->json(['errmsg' => session()->get('senderr')],406);
                }
                //return response()->json(['msg' => 'SMS succesfully sent','mask' => $totalmaskbal_1],200);
            } else if ($senderidtype == 'nomask') {
                if (session()->has('sendsuccess'))
                {
                    return response()->json(['msg' => session()->get('sendsuccess'),'nonmask' => $totalnonmaskbal_1,'MessageId' => $camid],200);
                }

                if (session()->has('senderr'))
                {
                    return response()->json(['errmsg' => session()->get('senderr')],406);
                }
                //return response()->json(['msg' => 'SMS succesfully sent','nomask' => $totalnonmaskbal_1],200);
            } else if ($senderidtype == 'voice') {
                if (session()->has('sendsuccess'))
                {
                    return response()->json(['msg' => session()->get('sendsuccess'),'voice' => $totalvoicebal_1,'MessageId' => $camid],200);
                }

                if (session()->has('senderr'))
                {
                    return response()->json(['errmsg' => session()->get('senderr')],406);
                }
                //return response()->json(['msg' => 'SMS succesfully sent','voice' => $totalvoicebal_1],200);
            } else {
                return 0;
            }
        } else {
            if (session()->has('sendsuccess'))
            {
                return response()->json(['msg' => session()->get('sendsuccess'),'mask' => $totalmaskbal_1,'MessageId' => $camid],200);
            }

            if (session()->has('senderr'))
            {
                return response()->json(['errmsg' => session()->get('senderr')],406);
            }
            //return response()->json(['errmsg' => 'There is an error, with the senderid, please contact with vendor'],406);
        }
    }

    public function btrcFileApproved(Request $request) {
        
        if (AppTemplate::where('id', $request->id)->exists())
        {
            $template = new TemplateResource(AppTemplate::where('id', $request->id)->first());

            if ($template->btrc_file_status == false) {
                AppTemplate::where('id', $request->id)
                            ->where('content_file','!=','')
                            ->where('btrc_file_status',false)
                            ->update([
                                'btrc_file_status' => true
                            ]);

                return back()->with('msg','Status updated successfully');
            } else {
                AppTemplate::where('id', $request->id)
                        ->where('content_file','!=','')
                        ->where('btrc_file_status',true)
                        ->update([
                            'btrc_file_status' => false
                        ]);

                return back()->with('msg','Status updated successfully');
            }
        }

        return back()->with('msg','Record Not Found');

    }

    public function saveTemplate(Request $request)
    {
        if ($request->frmmode == 'ins')
        {

        
            if (Auth::guard('root')->check())
            {
                $user_id = Auth::guard('root')->user()->id;
                $usertype = 'root';
            } elseif (Auth::guard('manager')->check()) {
                $user_id = Auth::guard('manager')->user()->id;
                $usertype = 'root';
            } else {
                $user_id = Auth::guard('web')->user()->id;
                $usertype = 'client';
            }

            if (Auth::guard('web')->check())
            {
                //return $response = $this->approvedTemplteUploadedFile($request);
                $file = $this->handlefile->addFile($request);

                $this->template->addTemplate([
                    'template_title' => $request->template_title,
                    'template_desc' => $request->template_desc,
                    'content_file' => $file,
                    'user_id' => $user_id,
                    'user_type' => $usertype,
                    'status' => false,
                    'frmmode' => $request->frmmode,
                    'id' => $request->id
                ]);

                return back()->with('msg','Template inserted successfully');
            }

            $this->template->addTemplate([
                'template_title' => $request->template_title,
                'template_desc' => $request->template_desc,
                'user_id' => $user_id,
                'user_type' => $usertype,
                'status' => $request->status,
                'frmmode' => $request->frmmode,
                'id' => $request->id
            ]);

            return back()->with('msg','Template inserted successfully');
        }


        if ($request->frmmode == 'edt')
        {
            if (AppTemplate::where('id', $request->id)->exists())
            {

                $template = new TemplateResource(AppTemplate::where('id', $request->id)->first());
                if ($template->user_type == 'root') {
                    $this->template->addTemplate([
                        'template_title' => $request->template_title,
                        'template_desc' => $request->template_desc,
                        'user_id' => $template->user_id,
                        'user_type' => $template->user_type,
                        'status' => $request->status,
                        'frmmode' => $request->frmmode,
                        'id' => $request->id
                    ]);

                    return back()->with('msg','Template updated successfully');
                }

                if ($template->user_type == 'client') {

                    //$this->approvedTemplteUploadedFile($request);

                    $file = $this->handlefile->addFile($request);

                    if ($template->status != true) {
                        $this->template->addTemplate([
                            'template_title' => $request->template_title,
                            'template_desc' => $request->template_desc,
                            'content_file' => $template->btrc_file_status ? NULL : $file,
                            'user_id' => $template->user_id,
                            'user_type' => $template->user_type,
                            'status' => $request->status,
                            'frmmode' => $request->frmmode,
                            'id' => $request->id
                        ]);
                    }

                    return back()->with('msg','Template updated successfully');
                }
            }
        }
    }
}
