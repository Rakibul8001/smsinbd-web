<?php

namespace App\Http\Controllers;

use App\CheckRuntimeBalance;
use App\Core\ClientSenderid\ClientSenderid;
use App\Core\ContactsAndGroups\ContactsAndGroups;
use App\Core\ContactsAndGroups\ContactsAndGroupsDetails;
use App\Core\ProductSales\ProductSales;
use App\Core\Senderid\SenderidDetails;
use App\Core\SmsSend\SmsSend;
use App\Core\SmsSend\SmsSendDetails;
use App\Core\UserCountSms\UserCountSms;
use App\Core\Users\ClientInterface;
use App\Core\Users\ClientRepository;
use App\Http\Resources\UserSentSmsResource;
use App\Jobs\PendingSmsToSendJob;
use App\Jobs\UserSmsSetupJob;
use App\Operator;
use App\SmsSender;
use App\Gateway;
use App\UserSentSms;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Core\Templates\Template;
use App\Http\Resources\SmsSenderIdResource;
use GuzzleHttp\Client;
use DB;

class ClientSmsSendController extends Controller
{
    
    /**
     * Client senderid service
     *
     * @var App\Core\ClientSenderid\ClientSenderidDetails
     */
    protected $clientsenderid;


    /**
     * Contact group service
     *
     * @var App\Core\ContactsAndGroups\ContactsAndGroupsDetails
     */
    protected $contactgroup;

    /**
     * SmsSend service
     *
     * @var App\Core\SmsSend\SmsSendDetails
     */
    protected $smssend;

    /**
     * User service
     *
     * @var App\Core\Users\ClientRepository
     */
    protected $client;

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
     * Total contacts in groups
     *
     * @var integer
     */
    protected $totalValidContact = 0;

    /**
     * Product sales service
     *
     * @var App\Core\ProductSales\ProductSaleDetails
     */
    protected $product;

    protected $consumesms;

    protected $template;

    public function __construct(
        ClientSenderid $clientsenderid,
        ContactsAndGroups $contactgroup,
        ClientInterface $client,
        SmsSend $smssend,
        ProductSales $product,
        UserCountSms $consumesms,
        Template $template
    )
    {
        $this->middleware('auth:web,root');
        $this->clientsenderid = $clientsenderid;
        $this->contactgroup = $contactgroup;
        $this->smssend = $smssend;
        $this->client = $client;
        $this->product = $product;
        $this->consumesms = $consumesms;
        $this->template = $template;

        ini_set('max_execution_time', 0);
    }

    /**
     * Populate sms send from
     *
     * @return void
     */
    public function showSendMsgFormDipping()
    {
        $data = [];
        $clientid = Auth::guard('web')->user();
        try{
            $clientsenderids = $this->clientsenderid->showClientSenderId($clientid);

            $clientmasksmsbal = $this->product->getSmsBalanceByCategory($clientid->id,'mask');
            $clientnonmasksmsbal = $this->product->getSmsBalanceByCategory($clientid->id,'nomask');
            $clientvoicemsbal = $this->product->getSmsBalanceByCategory($clientid->id,'voice');

            $totalmaskbal = ($clientmasksmsbal-$this->consumesms->totalConsumeMaskBalance($clientid->id));
            $totalnonmaskbal = ($clientnonmasksmsbal-$this->consumesms->totalConsumeNonMaskBalance($clientid->id));
            $totalvoicebal = ($clientvoicemsbal-$this->consumesms->totalConsumeVoiceBalance($clientid->id));
            
            if (count($clientsenderids) > 0) 
            {
                foreach($clientsenderids as $clientsender)
                {
                    
                    $rowid = 1;
                    if (count($clientsender->senderids) > 0)
                    {
                        foreach($clientsender->senderids as $senderid)
                        {
                            $data['data'][] = [
                                'user_sender_rec_id' => $senderid->id,
                                'user_id' => $senderid->user_id,
                                'default' => $senderid->default,
                                'clientname' => $senderid->client->name,
                                'sender_rec_id' => $senderid->senderClients->id,
                                'senderid' => $senderid->senderClients->sender_name,
                                'senderid_status' => $senderid->senderClients->status == 1 ? 'Yes' : 'No',
                                'created_at' => $clientsender->created_at->format('Y-m-d H:i:s'),

                            ];
                        }
                    } else {
                        $data['data'][] = [
                            'user_sender_rec_id' => '',
                            'user_id' => '',
                            'default' => '',
                            'clientname' => '',
                            'sender_rec_id' => '',
                            'senderid' => '',
                            'senderid_status' => '',
                            'created_at' => $clientsender->created_at->format('Y-m-d H:i:s'),

                        ];
                    }
                }

                $groups = $this->contactgroup->getGroupsByClient(Auth::guard('web')->user()->id);

                session()->put('totalmaskbal', $totalmaskbal);
                session()->put('totalnonmaskbal', $totalnonmaskbal);
                session()->put('totalvoicebal', $totalvoicebal);


                return view('smsview.messaging.send-message-dipping',compact('data','groups','totalmaskbal','totalnonmaskbal','totalvoicebal'));
            }
        } catch(\Exception $e) {
            return response()->json(['errmsg' => 'Settings matching, please check all necessary settings'], 200);
        }
    }


    public function getSenderIdType(Request $request)
    {
        if (! $request->has('senderid'))
        {
            return response()->json(['errmsg' => 'Senderid Not Provided'], 406);
        }
        
        $senderidtype = $this->smssend->getSenderIdType(['senderid' => $request->senderid]);

        //Cache::store('redis')->put('smscategory_client_'.Auth::guard('web')->user()->id, $senderidtype);

        return $senderidtype;
    }

    

    public function getTotalNumberOfContacts(Request $request)
    {

        if (! $request->has('contactgroup') || empty($request->contactgroup))
        {
            $totalContact = [];
            //Cache::store('redis')->put('totalcontacts_ina_group_client_'.Auth::guard('web')->user()->id, $totalContact);
            return response()->json(['errmsg' => $totalContact], 406);
        }

        $userid = Auth::guard('web')->user()->id;
        
        $totalContact = [];

        foreach($request->contactgroup as $group)
        {
            $groupinfo = $this->contactgroup->getGroupById($group);
            
            array_push($totalContact,$this->smssend->getTotalNumberOfContacts([
                'user_id' => $userid, 
                'groupid' => $group
            ]));
        }

        //Cache::store('redis')->put('totalcontacts_ina_group_client_'.Auth::guard('web')->user()->id, $totalContact);

        //return Cache::store('redis')->get('totalcontacts_ina_group_client_'.Auth::guard('web')->user()->id);
        return $totalContact;
    }


    public function validMobile(Request $request)
    {
        $userid = Auth::guard('web')->user()->id;

        $totalContacts = [];

        $newarr = [];
        if (! empty($request->contactgroup))
        {
            foreach(array_unique($request->contactgroup) as $group)
            {
                $totalContacts[] = $this->smssend->validMobile($userid, $group);

                $this->totalValidContact += $this->smssend->totalValidContactInAGroup();
            }
            
            foreach($totalContacts as $contact)
            {
                array_push($newarr,$contact);
            }
            
            return array_merge(...$newarr);
        }

        return 0;
    }

    public function validMobileFromFile(Request $request)
    {
        $userid = Auth::guard('web')->user()->id;

        $totalContacts = [];

        $newarr = [];

        $this->contactgroup->addContactFile($request);

        $extension = $this->contactgroup->getFileExtension();
        if ($extension === 'csv')
        {
            $contacts = $this->contactgroup->getBdMobileNumberFromCSV();

        } else if($extension === 'xls' || $extension === 'xlsx') {

            $contacts = $this->contactgroup->getBDMobileNumberFromXlsOrXlsx();


        } else if ($extension === 'txt') {
            
            $contacts = $this->contactgroup->getBDMobileNumberFromTextFile();


        } else {
            return response()->json(['msg' => 'There is an error, problem may be invalid file format!'], 406);
        }

        if (count($contacts) > 0 )
        {   
            $totalContacts[] = $this->smssend->validMobileFromFile($userid, $contacts);

            $this->totalValidContact += $this->smssend->totalValidContactInAFile();
            
            foreach($totalContacts as $contact)
            {
                array_push($newarr,$contact);
            }
            
            return array_merge(...$newarr);
        }

        return 0;
    }

    public function jobSms(Request $request)
    {
        $campaing = $request->campaing;
        $senderid = $request->senderid;

        $client = Auth::guard('web')->user();

        $userid = $client->id;
        $root_userid = $client->root_user_id;
        $reseller_id = $client->reseller_id;

        $sender = new SenderidDetails();
        $client = new ClientRepository();
        $contactandgroup = new ContactsAndGroupsDetails();
        $smssend = new SmsSendDetails($contactandgroup,$client,$sender);

        $contacts = DB::table('scheduled_smses')->where('user_id', $userid)
                                ->where('status',false)->get();
        

        if (! $contacts->isEmpty())
        {
            dispatch(new PendingSmsToSendJob($contacts,$smssend,$userid,$root_userid,$reseller_id, $senderid))->delay(now()->addSeconds(1));
        }
        
        
    }

    public function schedulingSms(Request $request){
        
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


        $contacts = [];

        $this->messagecount = $this->smssend->manageSmsMessageCount($request->message);

        $this->messagetype = $this->smssend->smsMessageType($request->message);

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

        //Single contact number start
        if ($request->numbertype == 'single')
        {
            $contacts = explode(",",str_replace("\n",",",str_replace(" ",",",$request->contact_number)));

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
            $contacts = $request->numbertype == 'contgroup' ? $this->validMobile($request) : $this->validMobileFromFile($request);

            $totalValidContact = $this->totalValidContact;

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

        foreach(array_unique($contacts) as $contact_number)
        {
            
            $validprefix =  ["88017","88014","88013","88015","88018","88016","88019"];

            $checkcontact = Str::substr($contact_number,0,2);
            $checkcontactwithplus = Str::substr($contact_number,0,3);

            //Covert number in format like 01716xxxxxx if start with 88
            $contact_number_n = '';
            $opt = '';
            if ($checkcontact == 88 || $checkcontactwithplus == '+88')
            {
                $contact_number_n = $checkcontact == 88 ? Str::substr($contact_number,0,13) : Str::substr($contact_number,1,13); 
                $opt = Str::substr($contact_number_n,0,5);
            } else {
                $contact_number_n = $contact_number;
                $opt = '88'.Str::substr($contact_number_n,0,3);
            }


            /*$contact_number_n = '';
            if ($checkcontact == 88 || $checkcontactwithplus == '+88')
            {
                $contact_number_n = $checkcontact == 88 ? Str::substr($contact_number,2,13) : Str::substr($contact_number,3,13); 
            } else {
                $contact_number_n = $contact_number;
            }

            $opt = '88'.Str::substr($contact_number_n,0,3); 
            */ 
                    
            if (in_array($opt, $validprefix))
            {
                if ($opt == "88017")
                {
                    $operator_contacts['gp'][] = [
                        'contact' => $contact_number_n,
                        'userid' => Auth::guard('web')->user()->id,
                        'remarks' => $campaing,
                        'number_of_sms' => $this->messagecount,
                        'sms_catagory' => $senderidtype,
                        'sms_type' => $this->messagetype,
                        'sms_content' => $request->message,
                        'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                        'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                        //'user_sent_sms_id' => $sent_sms->id
                    ];

                    $smssentdata[] = [
                        'remarks' => $campaing,
                        'user_id' => Auth::guard('web')->user()->id,
                        'user_sender_id' => $clientsenderids->id,
                        'to_number' => $contact_number_n,
                        'sms_type' => $this->messagetype,
                        'sms_catagory' => $senderidtype,
                        'sms_content' => $request->message,
                        'number_of_sms' => $this->messagecount,
                        'total_contacts' => 1,
                        'send_type' => 'smsadmin',
                        'contact_group_id' => 0,
                        'status' => false,
                        'submitted_at' => $request->target_time,
                    ];
                    
                }

                if ($opt == "88013")
                {
                    $operator_contacts['gpx'][] = [
                        'contact' => $contact_number_n,
                        'userid' => Auth::guard('web')->user()->id,
                        'remarks' => $campaing,
                        'number_of_sms' => $this->messagecount,
                        'sms_catagory' => $senderidtype,
                        'sms_type' => $this->messagetype,
                        'sms_content' => $request->message,
                        'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                        'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                        //'user_sent_sms_id' => $sent_sms->id
                    ];

                    $smssentdata[] = [
                        'remarks' => $campaing,
                        'user_id' => Auth::guard('web')->user()->id,
                        'user_sender_id' => $clientsenderids->id,
                        'to_number' => $contact_number_n,
                        'sms_type' => $this->messagetype,
                        'sms_catagory' => $senderidtype,
                        'sms_content' => $request->message,
                        'number_of_sms' => $this->messagecount,
                        'total_contacts' => 1,
                        'send_type' => 'smsadmin',
                        'contact_group_id' => 0,
                        'status' => false,
                        'submitted_at' => $request->target_time,
                    ];
                }

                if ($opt == "88019")
                {
                    $operator_contacts['blink'][] = [
                        'contact' => $contact_number_n,
                        'userid' => Auth::guard('web')->user()->id,
                        'remarks' => $campaing,
                        'number_of_sms' => $this->messagecount,
                        'sms_catagory' => $senderidtype,
                        'sms_type' => $this->messagetype,
                        'sms_content' => $request->message,
                        'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                        'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                        //'user_sent_sms_id' => $sent_sms->id
                    ];

                    $smssentdata[] = [
                        'remarks' => $campaing,
                        'user_id' => Auth::guard('web')->user()->id,
                        'user_sender_id' => $clientsenderids->id,
                        'to_number' => $contact_number_n,
                        'sms_type' => $this->messagetype,
                        'sms_catagory' => $senderidtype,
                        'sms_content' => $request->message,
                        'number_of_sms' => $this->messagecount,
                        'total_contacts' => 1,
                        'send_type' => 'smsadmin',
                        'contact_group_id' => 0,
                        'status' => false,
                        'submitted_at' => $request->target_time,
                    ];
                }

                if ($opt == "88014")
                {
                    $operator_contacts['blx'][] = [
                        'contact' => $contact_number_n,
                        'userid' => Auth::guard('web')->user()->id,
                        'remarks' => $campaing,
                        'number_of_sms' => $this->messagecount,
                        'sms_catagory' => $senderidtype,
                        'sms_type' => $this->messagetype,
                        'sms_content' => $request->message,
                        'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                        'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                        //'user_sent_sms_id' => $sent_sms->id
                    ];

                    $smssentdata[] = [
                        'remarks' => $campaing,
                        'user_id' => Auth::guard('web')->user()->id,
                        'user_sender_id' => $clientsenderids->id,
                        'to_number' => $contact_number_n,
                        'sms_type' => $this->messagetype,
                        'sms_catagory' => $senderidtype,
                        'sms_content' => $request->message,
                        'number_of_sms' => $this->messagecount,
                        'total_contacts' => 1,
                        'send_type' => 'smsadmin',
                        'contact_group_id' => 0,
                        'status' => false,
                        'submitted_at' => $request->target_time,
                    ];
                }

                if ($opt == "88015")
                {
                    $operator_contacts['teletalk'][] = [
                        'contact' => $contact_number_n,
                        'userid' => Auth::guard('web')->user()->id,
                        'remarks' => $campaing,
                        'number_of_sms' => $this->messagecount,
                        'sms_catagory' => $senderidtype,
                        'sms_type' => $this->messagetype,
                        'sms_content' => $request->message,
                        'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                        'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                        //'user_sent_sms_id' => $sent_sms->id
                    ];

                    $smssentdata[] = [
                        'remarks' => $campaing,
                        'user_id' => Auth::guard('web')->user()->id,
                        'user_sender_id' => $clientsenderids->id,
                        'to_number' => $contact_number_n,
                        'sms_type' => $this->messagetype,
                        'sms_catagory' => $senderidtype,
                        'sms_content' => $request->message,
                        'number_of_sms' => $this->messagecount,
                        'total_contacts' => 1,
                        'send_type' => 'smsadmin',
                        'contact_group_id' => 0,
                        'status' => false,
                        'submitted_at' => $request->target_time,
                    ];
                }

                if ($opt == "88018")
                {
                    $operator_contacts['robi'][] = [
                        'contact' => $contact_number_n,
                        'userid' => Auth::guard('web')->user()->id,
                        'remarks' => $campaing,
                        'number_of_sms' => $this->messagecount,
                        'sms_catagory' => $senderidtype,
                        'sms_type' => $this->messagetype,
                        'sms_content' => $request->message,
                        'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                        'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                        //'user_sent_sms_id' => $sent_sms->id
                    ];

                    $smssentdata[] = [
                        'remarks' => $campaing,
                        'user_id' => Auth::guard('web')->user()->id,
                        'user_sender_id' => $clientsenderids->id,
                        'to_number' => $contact_number_n,
                        'sms_type' => $this->messagetype,
                        'sms_catagory' => $senderidtype,
                        'sms_content' => $request->message,
                        'number_of_sms' => $this->messagecount,
                        'total_contacts' => 1,
                        'send_type' => 'smsadmin',
                        'contact_group_id' => 0,
                        'status' => false,
                        'submitted_at' => $request->target_time,
                    ];
                }

                if ($opt == "88016")
                {
                    $operator_contacts['airtel'][] = [
                        'contact' => $contact_number_n,
                        'userid' => Auth::guard('web')->user()->id,
                        'remarks' => $campaing,
                        'number_of_sms' => $this->messagecount,
                        'sms_catagory' => $senderidtype,
                        'sms_type' => $this->messagetype,
                        'sms_content' => $request->message,
                        'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                        'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                        //'user_sent_sms_id' => $sent_sms->id
                    ];

                    $smssentdata[] = [
                        'remarks' => $campaing,
                        'user_id' => Auth::guard('web')->user()->id,
                        'user_sender_id' => $clientsenderids->id,
                        'to_number' => $contact_number_n,
                        'sms_type' => $this->messagetype,
                        'sms_catagory' => $senderidtype,
                        'sms_content' => $request->message,
                        'number_of_sms' => $this->messagecount,
                        'total_contacts' => 1,
                        'send_type' => 'smsadmin',
                        'contact_group_id' => 0,
                        'status' => false,
                        'submitted_at' => $request->target_time,
                    ];
                }

                if ($opt == "88099")
                {
                    $operator_contacts['rankstel'][] = [
                        'contact' => $contact_number_n,
                        'userid' => Auth::guard('web')->user()->id,
                        'remarks' => $campaing,
                        'number_of_sms' => $this->messagecount,
                        'sms_catagory' => $senderidtype,
                        'sms_type' => $this->messagetype,
                        'sms_content' => $request->message,
                        'owner_id' => $reseller_id > 0 ? $reseller_id : $root_userid,
                        'owner_type' => $reseller_id > 0 ? 'reseller' : 'root',
                        //'user_sent_sms_id' => $sent_sms->id
                    ];

                    $smssentdata[] = [
                        'remarks' => $campaing,
                        'user_id' => Auth::guard('web')->user()->id,
                        'user_sender_id' => $clientsenderids->id,
                        'to_number' => $contact_number_n,
                        'sms_type' => $this->messagetype,
                        'sms_catagory' => $senderidtype,
                        'sms_content' => $request->message,
                        'number_of_sms' => $this->messagecount,
                        'total_contacts' => 1,
                        'send_type' => 'smsadmin',
                        'contact_group_id' => 0,
                        'status' => false,
                        'submitted_at' => $request->target_time,
                    ];
                }
            }
        }     

        foreach (array_chunk($smssentdata,5000) as $t)  
        {
            DB::table('scheduled_smses')->insert($t); 
        }
        
        
        $scheduleinfo = DB::table('scheduled_smses')->where('status',false)->orderBy('submitted_at','asc')->first();

        $findday = date("l", strtotime($scheduleinfo->submitted_at));
        $findtime = date("H:i", strtotime($scheduleinfo->submitted_at));
        $findhour = date("H", strtotime($scheduleinfo->submitted_at));
        $findmin = (int)date("i", strtotime($scheduleinfo->submitted_at));

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

        $add_cron = "$findmin	$findhour	$finddaynum $findmonthnum	$daynumberfromweek	/usr/local/bin/php /home/smsinbd/login.smsinbd.com/artisan schedule:run >> /dev/null 2>&1"; 
				      $output = shell_exec('crontab -l'); 
					  append_cronjob($add_cron); 
					  nl2br($output);

        return response()->json(['msg' => 'Schedule sms setup completed'],200);
    }



    public function resendFailedSms(Request $request) {
        $responsebeg = "";
        
        $failedsmses = DB::table("user_sent_smses")->where('remarks','campaing-'.$request->remarks)->where('status',false)->get();

        if (! $failedsmses->isEmpty()) {
            $contacts = [];
            foreach($failedsmses as $contact) {
                
                $contacts[] = $contact->to_number;
                
            }

            $request->request->add(['contact_number' => $contacts]);
            $request->request->add(['numbertype' => 'single']);
            $request->request->add(['senderid' => $request->sender]);
            $request->request->add(['message' => $request->message]);

            $responsebeg = $this->manageSmsMessage($request);

            DB::table("user_sent_smses")
                ->where('remarks','campaing-'.$request->remarks)
                ->where('status',false)
                ->delete();
            
            return $responsebeg;
        }

        return response()->json(['errmsg' => 'No failed sms found'], 406);

    }

    


    public function manageBulkMessage()
    {
        return $this->smssend->manageBulkSms();
    }

    public function bulkSmsCampaing()
    {
        return view('smsview.messaging.sms-campaing-list');
    }

    public function developerDoc()
    {
        $data = [];
        $clientsenderids = $this->clientsenderid->showClientSenderId(Auth::guard('web')->user());

          
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

        return view('smsview.messaging.developer-doc',compact('data'));
    }

}
