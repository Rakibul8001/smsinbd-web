<?php

namespace App\Jobs;

use App\Core\Resolver\SmsSendResolver;
use App\Core\SmsSend\SmsSend;
use App\Operator;
use App\SmsSender;
use App\UserSentSms;
use App\Gateway;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PendingSmsToSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 0;
    
    protected $userid;
    
    Protected $root_userid;
    
    protected $reseller_id;

    protected $smssend;

    protected $contacts;

    protected $senderid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($contact,SmsSend $smssend, $userid, $root_userid, $reseller_id, $senderid)
    {
        $this->userid = $userid;
        $this->root_userid = $root_userid;
        $this->reseller_id = $reseller_id;

        $this->smssend = $smssend;

        $this->contacts = $contact;

        $this->senderid = $senderid;
    }

    /**
     * Execute the job
     *
     * @return void
     */
    public function handle()
    {
        $root_userid = $this->root_userid;

        $reseller_id = $this->reseller_id;
            
        $clientsenderids = SmsSender::where('id',$this->senderid)->where('status',1)->first();//$this->smssend->resolve()->getSenderIdInformationBySenderName($contact->user_sender_id);

        $gateways = json_decode($clientsenderids->gateway_info,true);

        if (empty($gateways))
        {
            $gateways = $clientsenderids;
        }
                    
        $validprefix =  ["88017","88014","88013","88015","88018","88016","88019"];

        $operator_contacts = [];
        foreach(array_unique($this->contacts) as $contact)
        {
            
            $validprefix =  ["88017","88014","88013","88015","88018","88016","88019"];

            $checkcontact = Str::substr($contact->to_number,0,2);
            $checkcontactwithplus = Str::substr($contact->to_number,0,3);

            //Covert number in format like 01716xxxxxx if start with 88
            $contact_number_n = '';
            if ($checkcontact == 88 || $checkcontactwithplus == '+88')
            {
                $contact_number_n = $checkcontact == 88 ? Str::substr($contact->to_number,2,13) : Str::substr($contact->to_number,3,13); 
            } else {
                $contact_number_n = $contact->to_number;
            }

            
            $opt = '88'.Str::substr($contact_number_n,0,3);  
                    
            if (in_array($opt, $validprefix))
            {
                if ($opt == "88017")
                {
                    $operator_contacts['gp'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $contact->user_id,
                        'remarks' => $contact->remarks,
                        'number_of_sms' => $contact->number_of_sms,
                        'sms_catagory' => $contact->sms_catagory,
                        'sms_type' => $contact->sms_type,
                        'sms_content' => $contact->sms_content,
                        'owner_id' => $root_userid > 0 ? $root_userid : $reseller_id,
                        'owner_type' => $root_userid > 0 ? 'root' : 'reseller',
                        'user_sent_sms_id' => $contact->id
                    ];
                }

                if ($opt == "88013")
                {
                    $operator_contacts['gpx'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $contact->user_id,
                        'remarks' => $contact->remarks,
                        'number_of_sms' => $contact->number_of_sms,
                        'sms_catagory' => $contact->sms_catagory,
                        'sms_type' => $contact->sms_type,
                        'sms_content' => $contact->sms_content,
                        'owner_id' => $root_userid > 0 ? $root_userid : $reseller_id,
                        'owner_type' => $root_userid > 0 ? 'root' : 'reseller',
                        'user_sent_sms_id' => $contact->id
                    ];
                }

                if ($opt == "88019")
                {
                    $operator_contacts['blink'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $contact->user_id,
                        'remarks' => $contact->remarks,
                        'number_of_sms' => $contact->number_of_sms,
                        'sms_catagory' => $contact->sms_catagory,
                        'sms_type' => $contact->sms_type,
                        'sms_content' => $contact->sms_content,
                        'owner_id' => $root_userid > 0 ? $root_userid : $reseller_id,
                        'owner_type' => $root_userid > 0 ? 'root' : 'reseller',
                        'user_sent_sms_id' => $contact->id
                    ];
                }

                if ($opt == "88014")
                {
                    $operator_contacts['blx'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $contact->user_id,
                        'remarks' => $contact->remarks,
                        'number_of_sms' => $contact->number_of_sms,
                        'sms_catagory' => $contact->sms_catagory,
                        'sms_type' => $contact->sms_type,
                        'sms_content' => $contact->sms_content,
                        'owner_id' => $root_userid > 0 ? $root_userid : $reseller_id,
                        'owner_type' => $root_userid > 0 ? 'root' : 'reseller',
                        'user_sent_sms_id' => $contact->id
                    ];
                }

                if ($opt == "88015")
                {
                    $operator_contacts['teletalk'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $contact->user_id,
                        'remarks' => $contact->remarks,
                        'number_of_sms' => $contact->number_of_sms,
                        'sms_catagory' => $contact->sms_catagory,
                        'sms_type' => $contact->sms_type,
                        'sms_content' => $contact->sms_content,
                        'owner_id' => $root_userid > 0 ? $root_userid : $reseller_id,
                        'owner_type' => $root_userid > 0 ? 'root' : 'reseller',
                        'user_sent_sms_id' => $contact->id
                    ];
                }

                if ($opt == "88018")
                {
                    $operator_contacts['robi'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $contact->user_id,
                        'remarks' => $contact->remarks,
                        'number_of_sms' => $contact->number_of_sms,
                        'sms_catagory' => $contact->sms_catagory,
                        'sms_type' => $contact->sms_type,
                        'sms_content' => $contact->sms_content,
                        'owner_id' => $root_userid > 0 ? $root_userid : $reseller_id,
                        'owner_type' => $root_userid > 0 ? 'root' : 'reseller',
                        'user_sent_sms_id' => $contact->id
                    ];
                }

                if ($opt == "88016")
                {
                    $operator_contacts['airtel'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $contact->user_id,
                        'remarks' => $contact->remarks,
                        'number_of_sms' => $contact->number_of_sms,
                        'sms_catagory' => $contact->sms_catagory,
                        'sms_type' => $contact->sms_type,
                        'sms_content' => $contact->sms_content,
                        'owner_id' => $root_userid > 0 ? $root_userid : $reseller_id,
                        'owner_type' => $root_userid > 0 ? 'root' : 'reseller',
                        'user_sent_sms_id' => $contact->id
                    ];
                }

                
                //$operator = Operator::where('operator_prefix',$opt)->first();

                //$smsgateway = $operator->gateway;
            }
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
                        //"cli"	  	    => $sender,     
                        "charset"	=> $opcontact['sms_type'], 
                        "sms"		=> $opcontact['sms_content'],
                        'owner_id' => $opcontact['owner_id'],
                        'owner_type' => $opcontact['owner_type'],
                        'post_url' =>  'http://bulksms.teletalk.com.bd/link_sms_send.php?op=SMS',
                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                    ];

                    $this->smssend->smsSendToTelitalk($data);
                }
                
            } else {
            
                switch($operator)
                {
                    case 'teletalk':
                        $gateway = collect($gateways)->where('sender_operator_name','Teletalk');

                        $associate_gateway = Gateway::where('id',$gateway[6]->associate_gateway)->first(); //Operator::where('id',$gateway[0]['associate_gateway'])->first();

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
                                    //"cli"	  	    => $sender,     
                                    "charset"	=> $opcontact['sms_type'], 
                                    "sms"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url' =>  $associate_gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];
                                
                                $this->smssend->smsSendToTelitalk($data);
                            }
                        } else if($associate_gateway->id == 2) {
                            //gp
                            $gp100 = [];
                            $i = 1;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['teletalk'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 3) {
                            //blink
                            $gp100 = [];
                            $i = 1;
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
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
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
                                            'sender'   => $clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        unset($gp100);
                                        $this->smssend->smsSendToBlink($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => $clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 4) {
                            //robi
                            $gp100 = [];
                            $i = 1;
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
                                    'To'       => $operator_contacts['teletalk'][0]['contact'],
                                    'messagecount' => $operator_contacts['teletalk'][0]['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRobi($data);
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
                                            'From'     => $clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        $this->smssend->smsSendToRobi($data);
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => $clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRobi($data);
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 5) {
                            //airtel
                            $gp100 = [];
                            $i = 1;
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
                                    'To'       => $operator_contacts['teletalk'][0]['contact'],
                                    'messagecount' => $operator_contacts['teletalk'][0]['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRobi($data);
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
                                            'From'     => $clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        $this->smssend->smsSendToRobi($data);
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => $clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRobi($data);
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 6) {
                            //blx
                            $gp100 = [];
                            $i = 1;
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
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
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
                                            'sender'   => $clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        unset($gp100);
                                        $this->smssend->smsSendToBlink($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => $clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 7) {
                            //gpx
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['teletalk'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 9) {
                            //gp-3
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['gp'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['gp'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 10) {
                            //gp-2
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['teletalk'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['teletalk'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['teletalk'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['teletalk'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['teletalk'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        }
                        break;
                    case 'gp':
                        $gateway = collect($gateways)->where('sender_operator_name','GP');
                        $associate_gateway = Gateway::where('id',$gateway[0]->associate_gateway)->first();
                        //$associate_gateway = Operator::where('id',$gateway[1]['associate_gateway'])->first();

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
                                    //"cli"	  	    => $sender,     
                                    "charset"	=> $opcontact['sms_type'], 
                                    "sms"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url' =>  $associate_gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToTelitalk($data);
                            }
                        } else if($associate_gateway->id == 2) {
                            //gp
                            $gp100 = [];
                            $i = 1;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['gp'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['gp'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 3) {
                            //blink
                            $gp100 = [];
                            $i = 1;
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
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['gp'][0]['user_sent_sms_id']
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
                                            'sender'   => $clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        unset($gp100);
                                        $this->smssend->smsSendToBlink($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => $clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 4) {
                            //robi
                            $gp100 = [];
                            $i = 1;
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
                                    'To'       => $operator_contacts['gp'][0]['contact'],
                                    'messagecount' => $operator_contacts['gp'][0]['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['gp'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRobi($data);
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
                                            'From'     => $clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        $this->smssend->smsSendToRobi($data);
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => $clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRobi($data);
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 5) {
                            //airtel
                            $gp100 = [];
                            $i = 1;
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
                                    'To'       => $operator_contacts['gp'][0]['contact'],
                                    'messagecount' => $operator_contacts['gp'][0]['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['gp'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRobi($data);
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
                                            'From'     => $clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        $this->smssend->smsSendToRobi($data);
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => $clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRobi($data);
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 6) {
                            //blx
                            $gp100 = [];
                            $i = 1;
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
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['gp'][0]['user_sent_sms_id']
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
                                            'sender'   => $clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        unset($gp100);
                                        $this->smssend->smsSendToBlink($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => $clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 7) {
                            //gpx
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['gp'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['gp'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 9) {
                            //gp-3
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['gp'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['gp'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 10) {
                            //gp-2
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['gp'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['gp'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gp'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gp'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['gp'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        }
                    break;
                    case 'gpx':
                        $gateway = collect($gateways)->where('sender_operator_name','GPx');

                        $associate_gateway = Gateway::where('id',$gateway[1]->associate_gateway)->first();
                        //$associate_gateway = Operator::where('id',$gateway[6]['associate_gateway'])->first();

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
                                    //"cli"	  	    => $sender,     
                                    "charset"	=> $opcontact['sms_type'], 
                                    "sms"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url' =>  $associate_gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToTelitalk($data);
                            }
                        } else if($associate_gateway->id == 2) {
                            //gp
                            $gp100 = [];
                            $i = 1;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['gpx'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 3) {
                            //blink
                            $gp100 = [];
                            $i = 1;
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
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
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
                                            'sender'   => $clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        unset($gp100);
                                        $this->smssend->smsSendToBlink($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => $clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 4) {
                            //robi
                            $gp100 = [];
                            $i = 1;
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
                                    'To'       => $operator_contacts['gpx'][0]['contact'],
                                    'messagecount' => $operator_contacts['gpx'][0]['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRobi($data);
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
                                            'From'     => $clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        $this->smssend->smsSendToRobi($data);
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => $clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRobi($data);
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 5) {
                            //airtel
                            $gp100 = [];
                            $i = 1;
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
                                    'To'       => $operator_contacts['gpx'][0]['contact'],
                                    'messagecount' => $operator_contacts['gpx'][0]['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRobi($data);
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
                                            'From'     => $clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        $this->smssend->smsSendToRobi($data);
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => $clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRobi($data);
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 6) {
                            //blx
                            $gp100 = [];
                            $i = 1;
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
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
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
                                            'sender'   => $clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        unset($gp100);
                                        $this->smssend->smsSendToBlink($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => $clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 7) {
                            //gpx
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['gpx'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 9) {
                            //gp-3
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['gpx'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 10) {
                            //gp-2
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['gpx'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['gpx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['gpx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['gpx'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['gpx'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        }
                    break;
                    case 'blink':
                        $gateway = collect($gateways)->where('sender_operator_name','Banglalink');

                        $associate_gateway = Gateway::where('id',$gateway[2]->associate_gateway)->first();
                        //$associate_gateway = Operator::where('id',$gateway[2]['associate_gateway'])->first();

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
                                    //"cli"	  	    => $sender,     
                                    "charset"	=> $opcontact['sms_type'], 
                                    "sms"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url' =>  $associate_gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToTelitalk($data);
                            }
                        } else if($associate_gateway->id == 2) {
                            //gp
                            $gp100 = [];
                            $i = 1;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['blink'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 3) {
                            //blink
                            $gp100 = [];
                            $i = 1;
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
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
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
                                            'sender'   => $clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        unset($gp100);
                                        $this->smssend->smsSendToBlink($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => $clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 4) {
                            //robi
                            $gp100 = [];
                            $i = 1;
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
                                    'To'       => $operator_contacts['blink'][0]['contact'],
                                    'messagecount' => $operator_contacts['blink'][0]['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRobi($data);
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
                                            'From'     => $clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        $this->smssend->smsSendToRobi($data);
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => $clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRobi($data);
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 5) {
                            //airtel
                            $gp100 = [];
                            $i = 1;
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
                                    'To'       => $operator_contacts['blink'][0]['contact'],
                                    'messagecount' => $operator_contacts['blink'][0]['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRobi($data);
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
                                            'From'     => $clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        $this->smssend->smsSendToRobi($data);
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => $clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRobi($data);
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 6) {
                            //blx
                            $gp100 = [];
                            $i = 1;
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
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
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
                                            'sender'   => $clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        unset($gp100);
                                        $this->smssend->smsSendToBlink($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => $clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 7) {
                            //gpx
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['blink'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 9) {
                            //gp-3
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['blink'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 10) {
                            //gp-2
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['blink'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['blink'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blink'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blink'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['blink'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        }
                    break;
                    case 'blx':
                        
                        $gateway = collect($gateways)->where('sender_operator_name','BLx');

                        $associate_gateway = Gateway::where('id',$gateway[3]->associate_gateway)->first();
                        //$associate_gateway = Operator::where('id',$gateway[5]['associate_gateway'])->first();

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
                                    //"cli"	  	    => $sender,     
                                    "charset"	=> $opcontact['sms_type'], 
                                    "sms"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url' =>  $associate_gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToTelitalk($data);
                            }
                        } else if($associate_gateway->id == 2) {
                            //gp
                            $gp100 = [];
                            $i = 1;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['blx'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 3) {
                            //blink
                            $gp100 = [];
                            $i = 1;
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
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
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
                                            'sender'   => $clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        unset($gp100);
                                        $this->smssend->smsSendToBlink($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => $clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 4) {
                            //robi
                            $gp100 = [];
                            $i = 1;
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
                                    'To'       => $operator_contacts['blx'][0]['contact'],
                                    'messagecount' => $operator_contacts['blx'][0]['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRobi($data);
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
                                            'From'     => $clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        $this->smssend->smsSendToRobi($data);
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => $clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRobi($data);
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 5) {
                            //airtel
                            $gp100 = [];
                            $i = 1;
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
                                    'To'       => $operator_contacts['blx'][0]['contact'],
                                    'messagecount' => $operator_contacts['blx'][0]['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRobi($data);
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
                                            'From'     => $clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        $this->smssend->smsSendToRobi($data);
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => $clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRobi($data);
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 6) {
                            //blx
                            $gp100 = [];
                            $i = 1;
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
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
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
                                            'sender'   => $clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        unset($gp100);
                                        $this->smssend->smsSendToBlink($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => $clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 7) {
                            //gpx
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['blx'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 9) {
                            //gp-3
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['blx'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 10) {
                            //gp-2
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['blx'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['blx'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['blx'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['blx'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['blx'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        }
                    break;
                    case 'robi':
                        
                        $gateway = collect($gateways)->where('sender_operator_name','Robi');

                        $associate_gateway = Gateway::where('id',$gateway[5]->associate_gateway)->first();
                        //$associate_gateway = Operator::where('id',$gateway[3]['associate_gateway'])->first();

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
                                    //"cli"	  	    => $sender,     
                                    "charset"	=> $opcontact['sms_type'], 
                                    "sms"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url' =>  $associate_gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToTelitalk($data);
                            }
                        } else if($associate_gateway->id == 2) {
                            //gp
                            $gp100 = [];
                            $i = 1;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['robi'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 3) {
                            //blink
                            $gp100 = [];
                            $i = 1;
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
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
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
                                            'sender'   => $clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        unset($gp100);
                                        $this->smssend->smsSendToBlink($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => $clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 4) {
                            //robi
                            $gp100 = [];
                            $i = 1;
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
                                    'To'       => $operator_contacts['robi'][0]['contact'],
                                    'messagecount' => $operator_contacts['robi'][0]['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRobi($data);
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
                                            'From'     => $clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        $this->smssend->smsSendToRobi($data);
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => $clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRobi($data);
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 5) {
                            //airtel
                            $gp100 = [];
                            $i = 1;
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
                                    'To'       => $operator_contacts['robi'][0]['contact'],
                                    'messagecount' => $operator_contacts['robi'][0]['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRobi($data);
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
                                            'From'     => $clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        $this->smssend->smsSendToRobi($data);
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => $clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRobi($data);
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 6) {
                            //blx
                            $gp100 = [];
                            $i = 1;
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
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
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
                                            'sender'   => $clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        unset($gp100);
                                        $this->smssend->smsSendToBlink($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => $clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 7) {
                            //gpx
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['robi'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 9) {
                            //gp-3
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['robi'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 10) {
                            //gp-2
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['robi'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['robi'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['robi'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['robi'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['robi'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        }
                    break;
                    case 'airtel':
                        
                        $gateway = collect($gateways)->where('sender_operator_name','Airtel');

                        $associate_gateway = Gateway::where('id',$gateway[4]->associate_gateway)->first();
                        //$associate_gateway = Operator::where('id',$gateway[4]['associate_gateway'])->first();

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
                                    //"cli"	  	    => $sender,     
                                    "charset"	=> $opcontact['sms_type'], 
                                    "sms"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url' =>  $associate_gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToTelitalk($data);
                            }
                        } else if($associate_gateway->id == 2) {
                            //gp
                            $gp100 = [];
                            $i = 1;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['airtel'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 3) {
                            //blink
                            $gp100 = [];
                            $i = 1;
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
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
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
                                            'sender'   => $clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        unset($gp100);
                                        $this->smssend->smsSendToBlink($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => $clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 4) {
                            //robi
                            $gp100 = [];
                            $i = 1;
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
                                    'To'       => $operator_contacts['airtel'][0]['contact'],
                                    'messagecount' => $operator_contacts['airtel'][0]['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRobi($data);
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
                                            'From'     => $clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        $this->smssend->smsSendToRobi($data);
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => $clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRobi($data);
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 5) {
                            //airtel
                            $gp100 = [];
                            $i = 1;
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
                                    'To'       => $operator_contacts['airtel'][0]['contact'],
                                    'messagecount' => $operator_contacts['airtel'][0]['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
                                ];
                                $this->smssend->smsSendToRobi($data);
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
                                            'From'     => $clientsenderids->sender_name,
                                            'Message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];
                                        $this->smssend->smsSendToRobi($data);
                                        unset($gp100);
                                    }
                                    //if ($i <= 200)
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }
                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'To'       => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'From'     => $clientsenderids->sender_name,
                                        'Message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToRobi($data);
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 6) {
                            //blx
                            $gp100 = [];
                            $i = 1;
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
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
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
                                            'sender'   => $clientsenderids->sender_name,
                                            'message'  => $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        ];

                                        unset($gp100);
                                        $this->smssend->smsSendToBlink($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
                                        continue;
                                    }

                                    $data = [
                                        'userid'   => $opcontact['userid'],
                                        'campaing' => $opcontact['remarks'],
                                        'senderidtype' => $opcontact['sms_catagory'],
                                        'post_url' => $associate_gateway->api_url,
                                        'msisdn'   => implode(",",$gp100),
                                        'messagecount' => $opcontact['number_of_sms'],
                                        'sender'   => $clientsenderids->sender_name,
                                        'message'  => $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    ];
                                    $this->smssend->smsSendToBlink($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 7) {
                            //gpx
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['airtel'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 9) {
                            //gp-3
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['airtel'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        } else if($associate_gateway->id == 10) {
                            //gp-2
                            $gp100 = [];
                            $i = 0;
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
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $operator_contacts['airtel'][0]['sms_type'], 
                                    "message"		=> $operator_contacts['airtel'][0]['sms_content'],
                                    'owner_id' => $operator_contacts['airtel'][0]['owner_id'],
                                    'owner_type' => $operator_contacts['airtel'][0]['owner_type'],
                                    'post_url'      => $associate_gateway->api_url,
                                    'user_sent_sms_id' => $operator_contacts['airtel'][0]['user_sent_sms_id']
                                    
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
                                            "cli"	  	    => $clientsenderids->sender_name,     
                                            "messagetype"	=> $opcontact['sms_type'], 
                                            "message"		=> $opcontact['sms_content'],
                                            'owner_id' => $opcontact['owner_id'],
                                            'owner_type' => $opcontact['owner_type'],
                                            'post_url'      => $associate_gateway->api_url,
                                            'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                            
                                        ];

                                        unset($gp100);

                                        $this->smssend->smsSendToGp($data);
                                    }
                                    if ($i <= count($operator_contacts[$operator]))
                                    {

                                        $gp100[] = $opcontact['contact'];
                                        $i++;
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
                                        "cli"	  	    => $clientsenderids->sender_name,     
                                        "messagetype"	=> $opcontact['sms_type'], 
                                        "message"		=> $opcontact['sms_content'],
                                        'owner_id' => $opcontact['owner_id'],
                                        'owner_type' => $opcontact['owner_type'],
                                        'post_url'      => $associate_gateway->api_url,
                                        'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                        
                                    ];
                                    $this->smssend->smsSendToGp($data);
                                    
                                }
                            }
                            //return $data;
                        }
                    break;
                }
            }

            
        }
        
        /*foreach($this->contacts as $contact)
        {
            $checkcontact = Str::substr($contact->to_number,0,2);
            $checkcontactwithplus = Str::substr($contact->to_number,0,3);

            //Covert number in format like 01716xxxxxx if start with 88
            $contact_number_n = '';
            if ($checkcontact == 88 || $checkcontactwithplus == '+88')
            {
                $contact_number_n = $checkcontact == 88 ? Str::substr($contact->to_number,2,13) : Str::substr($contact->to_number,3,13); 
            } else {
                $contact_number_n = $contact->to_number;
            }
            
            $opt = '88'.Str::substr($contact_number_n,0,3);  
            
            if (in_array($opt, $validprefix))
            {
                if ($opt == "88017")
                {
                    $operator_contacts['gp'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $contact->user_id,
                        'remarks' => $contact->remarks,
                        'number_of_sms' => $contact->number_of_sms,
                        'sms_catagory' => $contact->sms_catagory,
                        'sms_type' => $contact->sms_type,
                        'sms_content' => $contact->sms_content,
                        'owner_id' => $root_userid > 0 ? $root_userid : $reseller_id,
                        'owner_type' => $root_userid > 0 ? 'root' : 'reseller',
                        'user_sent_sms_id' => $contact->id
                    ];
                }

                if ($opt == "88013")
                {
                    $operator_contacts['gpx'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $contact->user_id,
                        'remarks' => $contact->remarks,
                        'number_of_sms' => $contact->number_of_sms,
                        'sms_catagory' => $contact->sms_catagory,
                        'sms_type' => $contact->sms_type,
                        'sms_content' => $contact->sms_content,
                        'owner_id' => $root_userid > 0 ? $root_userid : $reseller_id,
                        'owner_type' => $root_userid > 0 ? 'root' : 'reseller',
                        'user_sent_sms_id' => $contact->id
                    ];
                }

                if ($opt == "88019")
                {
                    $operator_contacts['blink'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $contact->user_id,
                        'remarks' => $contact->remarks,
                        'number_of_sms' => $contact->number_of_sms,
                        'sms_catagory' => $contact->sms_catagory,
                        'sms_type' => $contact->sms_type,
                        'sms_content' => $contact->sms_content,
                        'owner_id' => $root_userid > 0 ? $root_userid : $reseller_id,
                        'owner_type' => $root_userid > 0 ? 'root' : 'reseller',
                        'user_sent_sms_id' => $contact->id
                    ];
                }

                if ($opt == "88014")
                {
                    $operator_contacts['blx'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $contact->user_id,
                        'remarks' => $contact->remarks,
                        'number_of_sms' => $contact->number_of_sms,
                        'sms_catagory' => $contact->sms_catagory,
                        'sms_type' => $contact->sms_type,
                        'sms_content' => $contact->sms_content,
                        'owner_id' => $root_userid > 0 ? $root_userid : $reseller_id,
                        'owner_type' => $root_userid > 0 ? 'root' : 'reseller',
                        'user_sent_sms_id' => $contact->id
                    ];
                }

                if ($opt == "88015")
                {
                    $operator_contacts['teletalk'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $contact->user_id,
                        'remarks' => $contact->remarks,
                        'number_of_sms' => $contact->number_of_sms,
                        'sms_catagory' => $contact->sms_catagory,
                        'sms_type' => $contact->sms_type,
                        'sms_content' => $contact->sms_content,
                        'owner_id' => $root_userid > 0 ? $root_userid : $reseller_id,
                        'owner_type' => $root_userid > 0 ? 'root' : 'reseller',
                        'user_sent_sms_id' => $contact->id
                    ];
                }

                if ($opt == "88018")
                {
                    $operator_contacts['robi'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $contact->user_id,
                        'remarks' => $contact->remarks,
                        'number_of_sms' => $contact->number_of_sms,
                        'sms_catagory' => $contact->sms_catagory,
                        'sms_type' => $contact->sms_type,
                        'sms_content' => $contact->sms_content,
                        'owner_id' => $root_userid > 0 ? $root_userid : $reseller_id,
                        'owner_type' => $root_userid > 0 ? 'root' : 'reseller',
                        'user_sent_sms_id' => $contact->id
                    ];
                }

                if ($opt == "88016")
                {
                    $operator_contacts['airtel'][] = [
                        'contact' => $contact_number_n,
                        'userid' => $contact->user_id,
                        'remarks' => $contact->remarks,
                        'number_of_sms' => $contact->number_of_sms,
                        'sms_catagory' => $contact->sms_catagory,
                        'sms_type' => $contact->sms_type,
                        'sms_content' => $contact->sms_content,
                        'owner_id' => $root_userid > 0 ? $root_userid : $reseller_id,
                        'owner_type' => $root_userid > 0 ? 'root' : 'reseller',
                        'user_sent_sms_id' => $contact->id
                    ];
                }

                
                //$operator = Operator::where('operator_prefix',$opt)->first();

                //$smsgateway = $operator->gateway;
            }

        }
        

        foreach(array_keys($operator_contacts) as $operator)
        {
            //return gettype($operator_contacts[$operator]);
            
            switch($operator)
            {
                case 'teletalk':
                    $gateway = collect($gateways)->where('sender_operator_name','Teletalk');

                    $associate_gateway = Operator::where('id',$gateway[0]['associate_gateway'])->first();

                    if ($associate_gateway->gateway->id == 1)
                    {   //teletalk
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            $data = [ 
                                "userid"        => $opcontact['userid'],
                                "campaing"  => $opcontact['remarks'],
                                "senderidtype" => $opcontact['sms_catagory'],
                                "user"		=> $associate_gateway->gateway->user,                //$optUrlUser[$optID],               
                                "pass"		=> $associate_gateway->gateway->password,                //$optUrlPass[$optID],  
                                "op"		=> "SMS", 
                                "mobile"	    => $opcontact['contact'],
                                "messagecount" => $opcontact['number_of_sms'],  
                                //"cli"	  	    => $sender,     
                                "charset"	=> $opcontact['sms_type'], 
                                "sms"		=> $opcontact['sms_content'],
                                'owner_id' => $opcontact['owner_id'],
                                'owner_type' => $opcontact['owner_type'],
                                'post_url' =>  $associate_gateway->gateway->api_url,
                                'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                            ];
                            
                            $this->smssend->smsSendToTelitalk($data);
                        }
                    } else if($associate_gateway->gateway->id == 2) {
                        //gp
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 3) {
                        //blink
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToBlink($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 4) {
                        //robi
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 200)
                            {
                                
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 200)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToRobi($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 5) {
                        //airtel
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 200)
                            {
                                
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 200)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToRobi($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 6) {
                        //blx
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            if (count($gp100) == 100)
                            {
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToBlink($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 7) {
                        //gpx
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 9) {
                        //gp-3
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 10) {
                        //gp-2
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    }
                    break;
                case 'gp':
                    $gateway = collect($gateways)->where('sender_operator_name','GP');

                    $associate_gateway = Operator::where('id',$gateway[1]['associate_gateway'])->first();

                    if ($associate_gateway->gateway->id == 1)
                    {   //teletalk
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            $data = [ 
                                "userid"        => $opcontact['userid'],
                                "campaing"  => $opcontact['remarks'],
                                "senderidtype" => $opcontact['sms_catagory'],
                                "user"		=> $associate_gateway->gateway->user,                //$optUrlUser[$optID],               
                                "pass"		=> $associate_gateway->gateway->password,                //$optUrlPass[$optID],  
                                "op"		=> "SMS", 
                                "mobile"	    => $opcontact['contact'],
                                "messagecount" => $opcontact['number_of_sms'],  
                                //"cli"	  	    => $sender,     
                                "charset"	=> $opcontact['sms_type'], 
                                "sms"		=> $opcontact['sms_content'],
                                'owner_id' => $opcontact['owner_id'],
                                'owner_type' => $opcontact['owner_type'],
                                'post_url' =>  $associate_gateway->gateway->api_url,
                                'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                            ];
                            $this->smssend->smsSendToTelitalk($data);
                        }
                    } else if($associate_gateway->gateway->id == 2) {
                        //gp
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 3) {
                        //blink
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToBlink($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 4) {
                        //robi
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 200)
                            {
                                
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 200)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToRobi($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 5) {
                        //airtel
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 200)
                            {
                                
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 200)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToRobi($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 6) {
                        //blx
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            if (count($gp100) == 100)
                            {
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToBlink($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 7) {
                        //gpx
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 9) {
                        //gp-3
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 10) {
                        //gp-2
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    }
                break;
                case 'gpx':
                    $gateway = collect($gateways)->where('sender_operator_name','GPx');

                    $associate_gateway = Operator::where('id',$gateway[6]['associate_gateway'])->first();

                    if ($associate_gateway->gateway->id == 1)
                    {   //teletalk
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            $data = [ 
                                "userid"        => $opcontact['userid'],
                                "campaing"  => $opcontact['remarks'],
                                "senderidtype" => $opcontact['sms_catagory'],
                                "user"		=> $associate_gateway->gateway->user,                //$optUrlUser[$optID],               
                                "pass"		=> $associate_gateway->gateway->password,                //$optUrlPass[$optID],  
                                "op"		=> "SMS", 
                                "mobile"	    => $opcontact['contact'],
                                "messagecount" => $opcontact['number_of_sms'],  
                                //"cli"	  	    => $sender,     
                                "charset"	=> $opcontact['sms_type'], 
                                "sms"		=> $opcontact['sms_content'],
                                'owner_id' => $opcontact['owner_id'],
                                'owner_type' => $opcontact['owner_type'],
                                'post_url' =>  $associate_gateway->gateway->api_url,
                                'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                            ];
                            $this->smssend->smsSendToTelitalk($data);
                        }
                    } else if($associate_gateway->gateway->id == 2) {
                        //gp
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 3) {
                        //blink
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToBlink($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 4) {
                        //robi
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 200)
                            {
                                
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 200)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToRobi($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 5) {
                        //airtel
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 200)
                            {
                                
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 200)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToRobi($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 6) {
                        //blx
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            if (count($gp100) == 100)
                            {
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToBlink($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 7) {
                        //gpx
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 9) {
                        //gp-3
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            //$this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 10) {
                        //gp-2
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    }
                break;
                case 'blink':
                    $gateway = collect($gateways)->where('sender_operator_name','Banglalink');

                    $associate_gateway = Operator::where('id',$gateway[2]['associate_gateway'])->first();

                    if ($associate_gateway->gateway->id == 1)
                    {   //teletalk
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            $data = [ 
                                "userid"        => $opcontact['userid'],
                                "campaing"  => $opcontact['remarks'],
                                "senderidtype" => $opcontact['sms_catagory'],
                                "user"		=> $associate_gateway->gateway->user,                //$optUrlUser[$optID],               
                                "pass"		=> $associate_gateway->gateway->password,                //$optUrlPass[$optID],  
                                "op"		=> "SMS", 
                                "mobile"	    => $opcontact['contact'],
                                "messagecount" => $opcontact['number_of_sms'],  
                                //"cli"	  	    => $sender,     
                                "charset"	=> $opcontact['sms_type'], 
                                "sms"		=> $opcontact['sms_content'],
                                'owner_id' => $opcontact['owner_id'],
                                'owner_type' => $opcontact['owner_type'],
                                'post_url' =>  $associate_gateway->gateway->api_url,
                                'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                            ];
                            $this->smssend->smsSendToTelitalk($data);
                        }
                    } else if($associate_gateway->gateway->id == 2) {
                        //gp
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 3) {
                        //blink
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToBlink($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 4) {
                        //robi
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 200)
                            {
                                
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 200)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToRobi($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 5) {
                        //airtel
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 200)
                            {
                                
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 200)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToRobi($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 6) {
                        //blx
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            if (count($gp100) == 100)
                            {
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToBlink($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 7) {
                        //gpx
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 9) {
                        //gp-3
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 10) {
                        //gp-2
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    }
                break;
                case 'blx':
                    $gateway = collect($gateways)->where('sender_operator_name','BLx');

                    $associate_gateway = Operator::where('id',$gateway[5]['associate_gateway'])->first();

                    if ($associate_gateway->gateway->id == 1)
                    {   //teletalk
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            $data = [ 
                                "userid"        => $opcontact['userid'],
                                "campaing"  => $opcontact['remarks'],
                                "senderidtype" => $opcontact['sms_catagory'],
                                "user"		=> $associate_gateway->gateway->user,                //$optUrlUser[$optID],               
                                "pass"		=> $associate_gateway->gateway->password,                //$optUrlPass[$optID],  
                                "op"		=> "SMS", 
                                "mobile"	    => $opcontact['contact'],
                                "messagecount" => $opcontact['number_of_sms'],  
                                //"cli"	  	    => $sender,     
                                "charset"	=> $opcontact['sms_type'], 
                                "sms"		=> $opcontact['sms_content'],
                                'owner_id' => $opcontact['owner_id'],
                                'owner_type' => $opcontact['owner_type'],
                                'post_url' =>  $associate_gateway->gateway->api_url,
                                'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                            ];
                            $this->smssend->smsSendToTelitalk($data);
                        }
                    } else if($associate_gateway->gateway->id == 2) {
                        //gp
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 3) {
                        //blink
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToBlink($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 4) {
                        //robi
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 200)
                            {
                                
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 200)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToRobi($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 5) {
                        //airtel
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 200)
                            {
                                
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 200)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToRobi($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 6) {
                        //blx
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            if (count($gp100) == 100)
                            {
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToBlink($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 7) {
                        //gpx
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 9) {
                        //gp-3
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 10) {
                        //gp-2
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    }
                break;
                case 'robi':
                    $gateway = collect($gateways)->where('sender_operator_name','Robi');

                    $associate_gateway = Operator::where('id',$gateway[3]['associate_gateway'])->first();

                    if ($associate_gateway->gateway->id == 1)
                    {   //teletalk
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            $data = [ 
                                "userid"        => $opcontact['userid'],
                                "campaing"  => $opcontact['remarks'],
                                "senderidtype" => $opcontact['sms_catagory'],
                                "user"		=> $associate_gateway->gateway->user,                //$optUrlUser[$optID],               
                                "pass"		=> $associate_gateway->gateway->password,                //$optUrlPass[$optID],  
                                "op"		=> "SMS", 
                                "mobile"	    => $opcontact['contact'],
                                "messagecount" => $opcontact['number_of_sms'],  
                                //"cli"	  	    => $sender,     
                                "charset"	=> $opcontact['sms_type'], 
                                "sms"		=> $opcontact['sms_content'],
                                'owner_id' => $opcontact['owner_id'],
                                'owner_type' => $opcontact['owner_type'],
                                'post_url' =>  $associate_gateway->gateway->api_url,
                                'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                            ];
                            $this->smssend->smsSendToTelitalk($data);
                        }
                    } else if($associate_gateway->gateway->id == 2) {
                        //gp
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 3) {
                        //blink
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToBlink($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 4) {
                        //robi
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 200)
                            {
                                
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 200)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToRobi($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 5) {
                        //airtel
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 200)
                            {
                                
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 200)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToRobi($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 6) {
                        //blx
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            if (count($gp100) == 100)
                            {
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToBlink($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 7) {
                        //gpx
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 9) {
                        //gp-3
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 10) {
                        //gp-2
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    }
                break;
                case 'airtel':
                    $gateway = collect($gateways)->where('sender_operator_name','Airtel');

                    $associate_gateway = Operator::where('id',$gateway[4]['associate_gateway'])->first();

                    if ($associate_gateway->gateway->id == 1)
                    {   //teletalk
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            $data = [ 
                                "userid"        => $opcontact['userid'],
                                "campaing"  => $opcontact['remarks'],
                                "senderidtype" => $opcontact['sms_catagory'],
                                "user"		=> $associate_gateway->gateway->user,                //$optUrlUser[$optID],               
                                "pass"		=> $associate_gateway->gateway->password,                //$optUrlPass[$optID],  
                                "op"		=> "SMS", 
                                "mobile"	    => $opcontact['contact'],
                                "messagecount" => $opcontact['number_of_sms'],  
                                //"cli"	  	    => $sender,     
                                "charset"	=> $opcontact['sms_type'], 
                                "sms"		=> $opcontact['sms_content'],
                                'owner_id' => $opcontact['owner_id'],
                                'owner_type' => $opcontact['owner_type'],
                                'post_url' =>  $associate_gateway->gateway->api_url,
                                'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                            ];
                            $this->smssend->smsSendToTelitalk($data);
                        }
                    } else if($associate_gateway->gateway->id == 2) {
                        //gp
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 3) {
                        //blink
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToBlink($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 4) {
                        //robi
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 200)
                            {
                                
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 200)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToRobi($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 5) {
                        //airtel
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 200)
                            {
                                
                                $data = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'To'       => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'From'     => $clientsenderids->sender_name,
                                    'Message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 200)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToRobi($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 6) {
                        //blx
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            if (count($gp100) == 100)
                            {
                                $data[] = [
                                    'userid'   => $opcontact['userid'],
                                    'campaing' => $opcontact['remarks'],
                                    'senderidtype' => $opcontact['sms_catagory'],
                                    'post_url' => $associate_gateway->gateway->api_url,
                                    'msisdn'   => implode(",",$gp100),
                                    'messagecount' => $opcontact['number_of_sms'],
                                    'sender'   => $clientsenderids->sender_name,
                                    'message'  => $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToBlink($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 7) {
                        //gpx
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data[] = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 9) {
                        //gp-3
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data[] = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    } else if($associate_gateway->gateway->id == 10) {
                        //gp-2
                        $gp100 = [];
                        $i = 0;
                        $data = [];
                        foreach($operator_contacts[$operator] as $opcontact)
                        {
                            
                            if (count($gp100) == 100)
                            {
                                $data[] = [ 
                                    "userid"        => $opcontact['userid'],
                                    "campaing"      => $opcontact['remarks'],
                                    "senderidtype" => $opcontact['sms_catagory'],
                                    "username"		=> $associate_gateway->gateway->user,               
                                    "password"		=> $associate_gateway->gateway->password,  
                                    "apicode"		=> "1", 
                                    "msisdn"	    => implode(",",$gp100),
                                    "messagecount" => $opcontact['number_of_sms'],
                                    "countrycode"	=> "880",
                                    "messageid"		=> 0,
                                    "cli"	  	    => $clientsenderids->sender_name,     
                                    "messagetype"	=> $opcontact['sms_type'], 
                                    "message"		=> $opcontact['sms_content'],
                                    'owner_id' => $opcontact['owner_id'],
                                    'owner_type' => $opcontact['owner_type'],
                                    'post_url'      => $associate_gateway->gateway->api_url,
                                    'user_sent_sms_id' => $opcontact['user_sent_sms_id']
                                    
                                ];

                                unset($gp100);
                            }
                            if ($i <= 100)
                            {

                                $gp100[] = $opcontact['contact'];
                                continue;
                            }
                            $this->smssend->smsSendToGp($data);
                            $i++;
                        }
                        //return $data;
                    }
                break;
            }

            
        }
        */
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    /*public function handle()
    {

            $clientsenderids = SmsSender::where('id',$this->contact->user_sender_id)->where('status',1)->first();//$this->smssend->resolve()->getSenderIdInformationBySenderName($contact->user_sender_id);

            $gateways = json_decode($clientsenderids->gateway_info);

            $validprefix =  ["88017","88014","88013","88015","88018","88016","88019"];

            $checkcontact = Str::substr($this->contact->to_number,0,2);
            $checkcontactwithplus = Str::substr($this->contact->to_number,0,3);

            //Covert number in format like 01716xxxxxx if start with 88
            $contact_number_n = '';
            if ($checkcontact == 88 || $checkcontactwithplus == '+88')
            {
                $contact_number_n = $checkcontact == 88 ? Str::substr($this->contact->to_number,2,13) : Str::substr($this->contact->to_number,3,13); 
            } else {
                $contact_number_n = $this->contact->to_number;
            }
            
            $opt = '88'.Str::substr($contact_number_n,0,3);  
                    
            if (in_array($opt, $validprefix))
            {
                $operator = Operator::where('operator_prefix',$opt)->first();

                $smsgateway = $operator->gateway;
            }

            foreach($gateways as $gateway)
            {

                if ($gateway->sender_operator_name === $operator->operator_name)
                {
                    $associate_gateway = Operator::where('id',$gateway->associate_gateway)->first();

                    switch($associate_gateway->gateway->id)
                    {
                        case 1: //Teletak
                            
                            $data = [ 
                                "userid"        => $this->userid,
                                "campaing"  => $this->contact->remarks,
                                "senderidtype" => $this->contact->sms_catagory,
                                "user"		=> $associate_gateway->gateway->user,                //$optUrlUser[$optID],               
                                "pass"		=> $associate_gateway->gateway->password,                //$optUrlPass[$optID],  
                                "op"		=> "SMS", 
                                "mobile"	    => $contact_number_n,
                                "messagecount" => $this->contact->number_of_sms,  
                                //"cli"	  	    => $sender,     
                                "charset"	=> $this->contact->sms_type, 
                                "sms"		=> $this->contact->sms_content,
                                'owner_id' => $this->root_userid > 0 ? $this->root_userid : $this->reseller_id,
                                'owner_type' => $this->root_userid > 0 ? 'root' : 'reseller',
                                'post_url' =>  $associate_gateway->gateway->api_url,
                                'user_sent_sms_id' => $this->contact->id
                            ];

                            $this->smssend->smsSendToTelitalk($data);
                            break;
                        case 2: //GP

                            $data = [ 
                                "userid"        => $this->userid,
                                "campaing"      => $this->contact->remarks,
                                "senderidtype" => $this->contact->sms_catagory,
                                "username"		=> $associate_gateway->gateway->user,               
                                "password"		=> $associate_gateway->gateway->password,  
                                "apicode"		=> "1", 
                                "msisdn"	    => $contact_number_n,
                                "messagecount" => $this->contact->number_of_sms,
                                "countrycode"	=> "880",
                                "messageid"		=> 0,
                                "cli"	  	    => $clientsenderids->sender_name,     
                                "messagetype"	=> $this->contact->sms_type, 
                                "message"		=> $this->contact->sms_content,
                                'owner_id' => $this->root_userid > 0 ? $this->root_userid : $this->reseller_id,
                                'owner_type' => $this->root_userid > 0 ? 'root' : 'reseller',
                                'post_url'      => $associate_gateway->gateway->api_url,
                                'user_sent_sms_id' => $this->contact->id
                                
                            ];

                            $this->smssend->smsSendToGp($data);

                            break;

                        case 3: //Bangla Link
                            $data = [
                                'userid'   => $this->userid,
                                'campaing' => $this->contact->remarks,
                                'senderidtype' => $this->contact->sms_catagory,
                                'post_url' => $associate_gateway->gateway->api_url,
                                'msisdn'   => $contact_number_n,
                                'messagecount' => $this->contact->number_of_sms,
                                'sender'   => $clientsenderids->sender_name,
                                'message'  => $this->contact->sms_content,
                                'owner_id' => $this->root_userid > 0 ? $this->root_userid : $this->reseller_id,
                                'owner_type' => $this->root_userid > 0 ? 'root' : 'reseller',
                                'user_sent_sms_id' => $this->contact->id
                            ];
                            
                            $this->smssend->smsSendToBlink($data);

                            break;

                        case 4: //Robi
                            
                            $data = [
                                'userid'   => $this->userid,
                                'campaing' => $this->contact->remarks,
                                'senderidtype' => $this->contact->sms_catagory,
                                'post_url' => $associate_gateway->gateway->api_url,
                                'To'       => $contact_number_n,
                                'messagecount' => $this->messagecount,
                                'From'     => $clientsenderids->sender_name,
                                'Message'  => $this->contact->sms_content,
                                'owner_id' => $this->root_userid > 0 ? $this->root_userid : $this->reseller_id,
                                'owner_type' => $this->root_userid > 0 ? 'root' : 'reseller',
                                'user_sent_sms_id' => $this->contact->id
                            ];
                            
                            $this->smssend->smsSendToRobi($data);

                            break;
                        case 5: //Airtel
                            $data = [
                                'userid'   => $this->userid,
                                'campaing' => $this->contact->remarks,
                                'senderidtype' => $this->contact->sms_catagory,
                                'post_url' => $associate_gateway->gateway->api_url,
                                'To'       => $contact_number_n,
                                'messagecount' => $this->messagecount,
                                'From'     => $clientsenderids->sender_name,
                                'Message'  => $this->contact->sms_content,
                                'owner_id' => $this->root_userid > 0 ? $this->root_userid : $this->reseller_id,
                                'owner_type' => $this->root_userid > 0 ? 'root' : 'reseller',
                                'user_sent_sms_id' => $this->contact->id
                            ];
                            
                            $this->smssend->smsSendToRobi($data);

                            break;
                        case 6: //Banglalink BLx
                            $data = [
                                'userid'   => $this->userid,
                                'campaing' => $this->contact->remarks,
                                'senderidtype' => $this->contact->sms_catagory,
                                'post_url' => $associate_gateway->gateway->api_url,
                                'msisdn'   => $contact_number_n,
                                'messagecount' => $this->contact->number_of_sms,
                                'sender'   => $clientsenderids->sender_name,
                                'message'  => $this->contact->sms_content,
                                'owner_id' => $this->root_userid > 0 ? $this->root_userid : $this->reseller_id,
                                'owner_type' => $this->root_userid > 0 ? 'root' : 'reseller',
                                'user_sent_sms_id' => $this->contact->id
                            ];
                            
                            $this->smssend->smsSendToBlink($data);
                            
                            break;
                        case 7: //GPx
                            $data = [ 
                                "userid"        => $this->userid,
                                "campaing"      => $this->contact->remarks,
                                "senderidtype" => $this->contact->sms_catagory,
                                "username"		=> $associate_gateway->gateway->user,               
                                "password"		=> $associate_gateway->gateway->password,  
                                "apicode"		=> "1", 
                                "msisdn"	    => $contact_number_n,
                                "messagecount" => $this->contact->number_of_sms,
                                "countrycode"	=> "880",
                                "messageid"		=> 0,
                                "cli"	  	    => $clientsenderids->sender_name,     
                                "messagetype"	=> $this->contact->sms_type, 
                                "message"		=> $this->contact->sms_content,
                                'owner_id' => $this->root_userid > 0 ? $this->root_userid : $this->reseller_id,
                                'owner_type' => $this->root_userid > 0 ? 'root' : 'reseller',
                                'post_url'      => $associate_gateway->gateway->api_url,
                                'user_sent_sms_id' => $this->contact->id
                                
                            ];

                            $this->smssend->smsSendToGp($data);
                            
                            break;
                        case 8:
                            break;
                        case 9: //GP-3
                            $data = [ 
                                "userid"        => $this->userid,
                                "campaing"      => $this->contact->remarks,
                                "senderidtype" => $this->contact->sms_catagory,
                                "username"		=> $associate_gateway->gateway->user,               
                                "password"		=> $associate_gateway->gateway->password,  
                                "apicode"		=> "1", 
                                "msisdn"	    => $contact_number_n,
                                "messagecount" => $this->contact->number_of_sms,
                                "countrycode"	=> "880",
                                "messageid"		=> 0,
                                "cli"	  	    => $clientsenderids->sender_name,     
                                "messagetype"	=> $this->contact->sms_type, 
                                "message"		=> $this->contact->sms_content,
                                'owner_id' => $this->root_userid > 0 ? $this->root_userid : $this->reseller_id,
                                'owner_type' => $this->root_userid > 0 ? 'root' : 'reseller',
                                'post_url'      => $associate_gateway->gateway->api_url,
                                'user_sent_sms_id' => $this->contact->id
                                
                            ];

                            $this->smssend->smsSendToGp($data);
                            
                            break;
                        case 10: //GP-2
                            $data = [ 
                                "userid"        => $this->userid,
                                "campaing"      => $this->contact->remarks,
                                "senderidtype" => $this->contact->sms_catagory,
                                "username"		=> $associate_gateway->gateway->user,               
                                "password"		=> $associate_gateway->gateway->password,  
                                "apicode"		=> "1", 
                                "msisdn"	    => $contact_number_n,
                                "messagecount" => $this->contact->number_of_sms,
                                "countrycode"	=> "880",
                                "messageid"		=> 0,
                                "cli"	  	    => $clientsenderids->sender_name,     
                                "messagetype"	=> $this->contact->sms_type, 
                                "message"		=> $this->contact->sms_content,
                                'owner_id' => $this->root_userid > 0 ? $this->root_userid : $this->reseller_id,
                                'owner_type' => $this->root_userid > 0 ? 'root' : 'reseller',
                                'post_url'      => $associate_gateway->gateway->api_url,
                                'user_sent_sms_id' => $this->contact->id
                                
                            ];

                            $this->smssend->smsSendToGp($data);
                            
                            break;
                        default:
                            return response()->json(['errmsg' => 'Invalid gateway'], 406);
                    }
                }
            }
    
    }*/
}
