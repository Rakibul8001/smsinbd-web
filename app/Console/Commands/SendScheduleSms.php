<?php

namespace App\Console\Commands;

use DB;
use App\User;
use App\Gateway;
use App\UserSentSms;
use Illuminate\Support\Str;
use App\Core\SmsSend\SmsSend;
use Illuminate\Console\Command;
use App\Core\SmsSend\SmsSendDetails;
use App\Core\ProductSales\ProductSales;
use App\Core\UserCountSms\UserCountSms;

class SendScheduleSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:smssend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send schedule sms to the contact';

    /**
     * Create a new command instance.
     *
     * @return void
     */

     /**
     * SmsSend service
     *
     * @var App\Core\SmsSend\SmsSendDetails
     */
    protected $smssend;

    /**
     * Product sales service
     *
     * @var App\Core\ProductSales\ProductSaleDetails
     */
    protected $product;

    protected $consumesms;

    public function __construct(SmsSend $smssend,ProductSales $product,UserCountSms $consumesms)
    {
        parent::__construct();

        $this->smssend = $smssend;

        $this->product = $product;

        $this->consumesms = $consumesms;

        ini_set('max_execution_time', 0);

        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$campaing = !empty($request->cam_name) ? $request->cam_name."-".Auth::guard('web')->user()->id : 'campaing-'.Auth::guard('web')->user()->id.date("d").mt_rand(10,100);

        $scheduleinfo = DB::table('scheduled_smses')->where('status',false)->orderBy('submitted_at','asc')->first();

        /*$findday = date("l", strtotime($scheduleinfo->submitted_at));
        $findtime = date("H:i", strtotime($scheduleinfo->submitted_at));
        $findhour = date("H", strtotime($scheduleinfo->submitted_at));
        $findmin = date("i", strtotime($scheduleinfo->submitted_at));

        $finddaynum = (int)date("d", strtotime($scheduleinfo->submitted_at));

        $findmonthnum = (int)date("m", strtotime($scheduleinfo->submitted_at));

        $day = strtolower($findday."s");

        $daynumberfromweek = 0;

        switch($findday)
        {
            case "Sunday":
                $daynumberfromweek = 0;
                break;
            case "Monday":
                $daynumberfromweek = 1;
                break;
            case "Tuesday":
                $daynumberfromweek = 2;
                break;
            case "Wednesday":
                $daynumberfromweek = 3;
                break;
            case "Thursday":
                $daynumberfromweek = 4;
                break;
            case "Friday":
                $daynumberfromweek = 5;
                break;
            case "Saturday":
                $daynumberfromweek = 6;
                break;
        }

        $add_cron = "$findmin	$findhour	$finddaynum $findmonthnum	$daynumberfromweek	/usr/local/bin/php /home/smsinbd/public_html/artisan schedule:run >> /dev/null 2>&1"; 
				      $output = shell_exec('crontab -l'); 
					  append_cronjob($add_cron); 
                      nl2br($output);
        */

        if(!DB::table('scheduled_smses')->where('status',false)->orderBy('submitted_at','asc')->exists())
        {
            return $this->error('No schedule found');
        }

        $clientsenderids = DB::table('sms_senders')->where('id', $scheduleinfo->user_sender_id)->first();

        if (! $clientsenderids->status)
        {
            return $this->error('Inactive Senderid');
        }

        $gateways = json_decode($clientsenderids->gateway_info);

        if (empty($gateways))
        {
            $gateways = $clientsenderids;
        }


        $contacts = [];

        $usercontacts = DB::table('scheduled_smses')->where('user_id',$scheduleinfo->user_id)->where('status',false)->orderBy('submitted_at','asc')->get();

        $this->messagecount = $this->smssend->manageSmsMessageCount($scheduleinfo->sms_content);

        $this->messagetype = $this->smssend->smsMessageType($scheduleinfo->sms_content);

        $senderidtype = $this->smssend->getSenderIdType(['senderid' => $clientsenderids->sender_name]);

        

        $clientid = User::where('id', $scheduleinfo->user_id)->first();

        $clientmasksmsbal = $this->product->getSmsBalanceByCategory($scheduleinfo->user_id,'mask');

        $clientnonmasksmsbal = $this->product->getSmsBalanceByCategory($clientid->id,'nomask');
        $clientvoicemsbal = $this->product->getSmsBalanceByCategory($clientid->id,'voice');

        $totalmaskbal = ($clientmasksmsbal-$this->consumesms->totalConsumeMaskBalance($clientid->id));
        $totalnonmaskbal = ($clientnonmasksmsbal-$this->consumesms->totalConsumeNonMaskBalance($clientid->id));
        $totalvoicebal = ($clientvoicemsbal-$this->consumesms->totalConsumeVoiceBalance($clientid->id));


        $root_userid = $clientid->root_user_id;

        $reseller_id = $clientid->reseller_id;

        //Single contact number start
        if (count($usercontacts) == 1)
        {
            $contacts = explode(",",$usercontacts[0]->to_number);

            $totalSms = ($this->messagecount*count($contacts));

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
            //$contacts = $request->numbertype == 'contgroup' ? $this->validMobile($request) : $this->validMobileFromFile($request);

            $totalValidContact = count($usercontacts);

            $totalSms = ($totalValidContact*$this->messagecount);

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

        foreach($usercontacts as $contact_number)
        {
            
            $validprefix =  ["88017","88014","88013","88015","88018","88016","88019"];

            $checkcontact = Str::substr($contact_number->to_number,0,2);
            $checkcontactwithplus = Str::substr($contact_number->to_number,0,3);

            //Covert number in format like 01716xxxxxx if start with 88
            $contact_number_n = '';
            
            $opt = '';
            if ($checkcontact == 88 || $checkcontactwithplus == '+88')
            {
                $contact_number_n = $checkcontact == 88 ? Str::substr($contact_number->to_number,0,13) : Str::substr($contact_number->to_number,1,13); 
                $opt = Str::substr($contact_number_n,0,5);
            } else {
                $contact_number_n = $contact_number->to_number;
                $opt = '88'.Str::substr($contact_number_n,0,3);
            }

            //$opt = '88'.Str::substr($contact_number_n,0,3);  
                    
            if (in_array($opt, $validprefix))
            {
                if ($opt == "88017")
                {
                    $operator_contacts['gp'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $scheduleinfo->user_id,
                        'remarks' => $scheduleinfo->remarks,
                        'number_of_sms' => $this->messagecount,
                        'sms_catagory' => $senderidtype,
                        'sms_type' => $this->messagetype,
                        'sms_content' => $scheduleinfo->sms_content,
                        'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                        'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                        //'user_sent_sms_id' => $sent_sms->id
                    ];

                    $smssentdata[] = [
                        'remarks' => $scheduleinfo->remarks,
                        'user_id' => $scheduleinfo->user_id,
                        'user_sender_id' => $scheduleinfo->user_sender_id,
                        'to_number' => $contact_number_n,
                        'sms_type' => $this->messagetype,
                        'sms_catagory' => $senderidtype,
                        'sms_content' => $scheduleinfo->sms_content,
                        'number_of_sms' => $this->messagecount,
                        'total_contacts' => 1,
                        'send_type' => 'smsadmin',
                        'contact_group_id' => 0,
                        'status' => false,
                        'submitted_at' => $scheduleinfo->submitted_at,
                    ];
                    
                }

                if ($opt == "88013")
                {
                    $operator_contacts['gpx'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $scheduleinfo->user_id,
                        'remarks' => $scheduleinfo->remarks,
                        'number_of_sms' => $this->messagecount,
                        'sms_catagory' => $senderidtype,
                        'sms_type' => $this->messagetype,
                        'sms_content' => $scheduleinfo->sms_content,
                        'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                        'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                        //'user_sent_sms_id' => $sent_sms->id
                    ];

                    $smssentdata[] = [
                        'remarks' => $scheduleinfo->remarks,
                        'user_id' => $scheduleinfo->user_id,
                        'user_sender_id' => $scheduleinfo->user_sender_id,
                        'to_number' => $contact_number_n,
                        'sms_type' => $this->messagetype,
                        'sms_catagory' => $senderidtype,
                        'sms_content' => $scheduleinfo->sms_content,
                        'number_of_sms' => $this->messagecount,
                        'total_contacts' => 1,
                        'send_type' => 'smsadmin',
                        'contact_group_id' => 0,
                        'status' => false,
                        'submitted_at' => $scheduleinfo->submitted_at,
                    ];
                }

                if ($opt == "88019")
                {
                    $operator_contacts['blink'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $scheduleinfo->user_id,
                        'remarks' => $scheduleinfo->remarks,
                        'number_of_sms' => $this->messagecount,
                        'sms_catagory' => $senderidtype,
                        'sms_type' => $this->messagetype,
                        'sms_content' => $scheduleinfo->sms_content,
                        'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                        'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                        //'user_sent_sms_id' => $sent_sms->id
                    ];

                    $smssentdata[] = [
                        'remarks' => $scheduleinfo->remarks,
                        'user_id' => $scheduleinfo->user_id,
                        'user_sender_id' => $scheduleinfo->user_sender_id,
                        'to_number' => $contact_number_n,
                        'sms_type' => $this->messagetype,
                        'sms_catagory' => $senderidtype,
                        'sms_content' => $scheduleinfo->sms_content,
                        'number_of_sms' => $this->messagecount,
                        'total_contacts' => 1,
                        'send_type' => 'smsadmin',
                        'contact_group_id' => 0,
                        'status' => false,
                        'submitted_at' => $scheduleinfo->submitted_at,
                    ];
                }

                if ($opt == "88014")
                {
                    $operator_contacts['blx'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $scheduleinfo->user_id,
                        'remarks' => $scheduleinfo->remarks,
                        'number_of_sms' => $this->messagecount,
                        'sms_catagory' => $senderidtype,
                        'sms_type' => $this->messagetype,
                        'sms_content' => $scheduleinfo->sms_content,
                        'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                        'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                        //'user_sent_sms_id' => $sent_sms->id
                    ];

                    $smssentdata[] = [
                        'remarks' => $scheduleinfo->remarks,
                        'user_id' => $scheduleinfo->user_id,
                        'user_sender_id' => $scheduleinfo->user_sender_id,
                        'to_number' => $contact_number_n,
                        'sms_type' => $this->messagetype,
                        'sms_catagory' => $senderidtype,
                        'sms_content' => $scheduleinfo->sms_content,
                        'number_of_sms' => $this->messagecount,
                        'total_contacts' => 1,
                        'send_type' => 'smsadmin',
                        'contact_group_id' => 0,
                        'status' => false,
                        'submitted_at' => $scheduleinfo->submitted_at,
                    ];
                }

                if ($opt == "88015")
                {
                    $operator_contacts['teletalk'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $scheduleinfo->user_id,
                        'remarks' => $scheduleinfo->remarks,
                        'number_of_sms' => $this->messagecount,
                        'sms_catagory' => $senderidtype,
                        'sms_type' => $this->messagetype,
                        'sms_content' => $scheduleinfo->sms_content,
                        'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                        'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                        //'user_sent_sms_id' => $sent_sms->id
                    ];

                    $smssentdata[] = [
                        'remarks' => $scheduleinfo->remarks,
                        'user_id' => $scheduleinfo->user_id,
                        'user_sender_id' => $scheduleinfo->user_sender_id,
                        'to_number' => $contact_number_n,
                        'sms_type' => $this->messagetype,
                        'sms_catagory' => $senderidtype,
                        'sms_content' => $scheduleinfo->sms_content,
                        'number_of_sms' => $this->messagecount,
                        'total_contacts' => 1,
                        'send_type' => 'smsadmin',
                        'contact_group_id' => 0,
                        'status' => false,
                        'submitted_at' => $scheduleinfo->submitted_at,
                    ];
                }

                if ($opt == "88018")
                {
                    $operator_contacts['robi'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $scheduleinfo->user_id,
                        'remarks' => $scheduleinfo->remarks,
                        'number_of_sms' => $this->messagecount,
                        'sms_catagory' => $senderidtype,
                        'sms_type' => $this->messagetype,
                        'sms_content' => $scheduleinfo->sms_content,
                        'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                        'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                        //'user_sent_sms_id' => $sent_sms->id
                    ];

                    $smssentdata[] = [
                        'remarks' => $scheduleinfo->remarks,
                        'user_id' => $scheduleinfo->user_id,
                        'user_sender_id' => $scheduleinfo->user_sender_id,
                        'to_number' => $contact_number_n,
                        'sms_type' => $this->messagetype,
                        'sms_catagory' => $senderidtype,
                        'sms_content' => $scheduleinfo->sms_content,
                        'number_of_sms' => $this->messagecount,
                        'total_contacts' => 1,
                        'send_type' => 'smsadmin',
                        'contact_group_id' => 0,
                        'status' => false,
                        'submitted_at' => $scheduleinfo->submitted_at,
                    ];
                }

                if ($opt == "88016")
                {
                    $operator_contacts['airtel'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $scheduleinfo->user_id,
                        'remarks' => $scheduleinfo->remarks,
                        'number_of_sms' => $this->messagecount,
                        'sms_catagory' => $senderidtype,
                        'sms_type' => $this->messagetype,
                        'sms_content' => $scheduleinfo->sms_content,
                        'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                        'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                        //'user_sent_sms_id' => $sent_sms->id
                    ];

                    $smssentdata[] = [
                        'remarks' => $scheduleinfo->remarks,
                        'user_id' => $scheduleinfo->user_id,
                        'user_sender_id' => $scheduleinfo->user_sender_id,
                        'to_number' => $contact_number_n,
                        'sms_type' => $this->messagetype,
                        'sms_catagory' => $senderidtype,
                        'sms_content' => $scheduleinfo->sms_content,
                        'number_of_sms' => $this->messagecount,
                        'total_contacts' => 1,
                        'send_type' => 'smsadmin',
                        'contact_group_id' => 0,
                        'status' => false,
                        'submitted_at' => $scheduleinfo->submitted_at,
                    ];
                }

                if ($opt == "88099")
                {
                    $operator_contacts['rankstel'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $scheduleinfo->user_id,
                        'remarks' => $scheduleinfo->remarks,
                        'number_of_sms' => $this->messagecount,
                        'sms_catagory' => $senderidtype,
                        'sms_type' => $this->messagetype,
                        'sms_content' => $scheduleinfo->remarks,
                        'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                        'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                        //'user_sent_sms_id' => $sent_sms->id
                    ];

                    $smssentdata[] = [
                        'remarks' => $scheduleinfo->remarks,
                        'user_id' => $scheduleinfo->user_id,
                        'user_sender_id' => $scheduleinfo->user_sender_id,
                        'to_number' => $contact_number_n,
                        'sms_type' => $this->messagetype,
                        'sms_catagory' => $senderidtype,
                        'sms_content' => $scheduleinfo->sms_content,
                        'number_of_sms' => $this->messagecount,
                        'total_contacts' => 1,
                        'send_type' => 'smsadmin',
                        'contact_group_id' => 0,
                        'status' => false,
                        'submitted_at' => $scheduleinfo->submitted_at,
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

        DB::table('scheduled_smses')->where('user_id',$scheduleinfo->user_id)->where('status',false)->update([
            'status' => 1
        ]);

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
                                    'senderidtype' => $operator_contacgpxts['gpx'][0]['sms_catagory'],
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
                                    'senderidtype' => $operator_contacgpxts['blink'][0]['sms_catagory'],
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
                                    'senderidtype' => $operator_contacgpxts['blx'][0]['sms_catagory'],
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
                                    'senderidtype' => $operator_contacgpxts['robi'][0]['sms_catagory'],
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
                                    'senderidtype' => $operator_contacgpxts['airtel'][0]['sms_catagory'],
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
                                    'senderidtype' => $operator_contacgpxts['rankstel'][0]['sms_catagory'],
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

        $camid = substr($scheduleinfo->remarks,9, strlen($scheduleinfo->remarks));

        if ($totalmaskbal_1 <= $totalmaskbal || $totalnonmaskbal_1 <= $totalnonmaskbal || $totalvoicebal_1<= $totalvoicebal)
        {
            if ($senderidtype == 'mask')
            {
                if (session()->has('sendsuccess'))
                {
                    //return response()->json(['msg' => session()->get('sendsuccess'),'mask' => $totalmaskbal_1],200);
                    $this->info(session()->get('sendsuccess'));
                }

                if (session()->has('senderr'))
                {
                    //return response()->json(['errmsg' => session()->get('senderr')],406);
                    $this->error(session()->get('senderr'));
                }
                //return response()->json(['msg' => 'SMS succesfully sent','mask' => $totalmaskbal_1],200);
            } else if ($senderidtype == 'nomask') {
                if (session()->has('sendsuccess'))
                {
                    $this->info(session()->get('sendsuccess'));
                }

                if (session()->has('senderr'))
                {
                    $this->error(session()->get('senderr'));
                }
                //return response()->json(['msg' => 'SMS succesfully sent','nomask' => $totalnonmaskbal_1],200);
            } else if ($senderidtype == 'voice') {
                if (session()->has('sendsuccess'))
                {
                    $this->info(session()->get('sendsuccess'));
                }

                if (session()->has('senderr'))
                {
                    $this->error(session()->get('senderr'));
                }
                //return response()->json(['msg' => 'SMS succesfully sent','voice' => $totalvoicebal_1],200);
            } else {
                return 0;
            }
        } else {
            if (session()->has('sendsuccess'))
            {
                $this->info(session()->get('sendsuccess'));
            }

            if (session()->has('senderr'))
            {
                $this->error(session()->get('senderr'));
            }
            //return response()->json(['errmsg' => 'There is an error, with the senderid, please contact with vendor'],406);
        }
    }
}
