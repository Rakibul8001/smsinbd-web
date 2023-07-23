<?php

namespace App\Http\Controllers;

use App\Core\ClientSenderid\ClientSenderid;
use App\Core\OperatorsGateways\Operators;
use App\UserSender;
use App\Core\OperatorsGateways\OperatorsApi;
use App\Core\Senderid\SenderId;
use App\Core\Users\ClientInterface;
use App\Gateway;
use App\Http\Requests\SmsSenderIdRequest;
use App\Http\Resources\ClientSenderidResource;
use App\Http\Resources\SmsSenderIdResource;
use App\Http\Resources\TemplateResource;
use App\Http\Resources\UserResource;
use App\SmsSender;
use App\Template;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SenderidController extends Controller
{
    /**
     * Sender ID Service
     *
     * @var App\Core\Semderid\SenderidDetails
     */
    protected $senderid;

    /**
     * Api Gateway Service
     *
     * @var App\Core\OperatorsGateways\OperatorsApiDetails
     */
    protected $gateway;

    /**
     * Client service
     *
     * @var App\Core\Users\ClientRepository
     */
    protected $client;

    /**
     * Operators service
     *
     * @var App\Core\OperatorsGateways\OperatorsDetails
     */
    protected $operators;


    public function __construct(
        SenderId $senderid,
        ClientInterface $client,
        OperatorsApi $gateway,
        Operators $operators
    )
    {
        $this->middleware('auth:root,manager');

        $this->senderid = $senderid;

        $this->gateway = $gateway;

        $this->client = $client;

        $this->operators = $operators;
    }

    /**
     * Add Sms Sender ID
     *
     * @param SmsSenderIdRequest $request
     * @return void
     */
    public function addSenderId(SmsSenderIdRequest $request)
    {
        $formmode = ['ins','edt'];
        $frmmode = $request->frmmode;

        $url = $request->headers->get('referer');

        if (! in_array($frmmode, $formmode)) {
            return back()->with('errmsg','Form submission mode not specified');
        }

        switch($frmmode)
        {
            case "ins":
        
                if ($request->sendertype == 'general')
                {
                    
                    if (SmsSender::where('sender_name', $request->sender_name)->where('operator_id',NULL)->where('user',NULL)->exists()) {

                        return redirect($url)->with('errmsg','Senderid already exists');

                    }
                    
                    $i = 0;
                    $associate = [];
                    foreach($request->sender_operator_name as $operatorname) {
                        array_push($associate,[
                            'sender_operator_name' => $operatorname,
                            'associate_sender_id' => str_replace(" ",'\"',$request->associate_sender_id[$i]),
                            'edit_associate_id' => $request->edit_associate_id[$i],
                            'associate_gateway' => $request->gateway[$i]
                        ]);

                        $i++;
                    }
                    $this->senderid->addSenderId([
                        'sendertype' => $request->sendertype,
                        'sender_name' => $request->sender_name,
                        'status' => $request->status,
                        'default' => $request->default,
                        'gateway_info' => json_encode($associate),
                        'created_by' => Auth::guard('root')->user()->id,
                        'updated_by' => Auth::guard('root')->user()->id
                    ]);

                    return redirect($url)->with('msg','Senderid successfully created');
                }

                if ($request->sendertype == 'teletalk'){

                    if (SmsSender::where('sender_name', $request->sender_name)->where('operator_id','!=',NULL)->where('user','!=',NULL)->exists()) {

                        return redirect($url)->with('errmsg','Senderid already exists');

                    }

                    $this->senderid->addSenderId([
                        'sendertype' => $request->sendertype,
                        'sender_name' => $request->sender_name,
                        'operator_id' => $request->sender_operator_id,
                        'status' => $request->status,
                        'default' => $request->default,
                        'user' => $request->sender_user,
                        'password' => $request->sender_password,
                        'created_by' => Auth::guard('root')->user()->id,
                        'updated_by' => Auth::guard('root')->user()->id
                    ]);

                    return redirect($url)->with('msg','Senderid successfully created');
                }
                break;
            case "edt":
                if (! $request->has('smssender_rec_id')) {
                    return back()->with('errmsg','ID missing');
                } 

                if(! $this->senderid->isValidSenderId($request->smssender_rec_id))
                {
                    return back()->with('errmsg','Record not found');
                }

                if ($request->sendertype == 'general')
                {
                    
                    $i = 0;
                    $associate = [];
                    foreach($request->sender_operator_name as $operatorname) {
                        array_push($associate,[
                            'sender_operator_name' => $operatorname,
                            'associate_sender_id' => str_replace(" ",'\"',$request->associate_sender_id[$i]),
                            'edit_associate_id' => $request->edit_associate_id[$i],
                            'associate_gateway' => $request->gateway[$i]
                        ]);
        
                        $i++;
                    }
                    $this->senderid->updateSenderId([
                        'smssender_rec_id' => $request->smssender_rec_id,
                        'sendertype' => $request->sendertype,
                        'sender_name' => $request->sender_name,
                        'status' => $request->status,
                        'default' => $request->default,
                        'gateway_info' => json_encode($associate),
                        'created_by' => Auth::guard('root')->user()->id,
                        'updated_by' => Auth::guard('root')->user()->id
                    ]);
        
                    return redirect($url)->with('msg','Senderid successfully updated');
                }
        
                if ($request->sendertype == 'teletalk'){
                    $this->senderid->updateSenderId([
                        'smssender_rec_id' => $request->smssender_rec_id,
                        'sendertype' => $request->sendertype,
                        'sender_name' => $request->sender_name,
                        'operator_id' => $request->sender_operator_id,
                        'status' => $request->status,
                        'default' => $request->default,
                        'user' => $request->sender_user,
                        'password' => $request->sender_password,
                        'created_by' => Auth::guard('root')->user()->id,
                        'updated_by' => Auth::guard('root')->user()->id
                    ]);
        
                    return redirect($url)->with('msg','Senderid successfully updated');
                }
                break;

                default:
                    return back()->with('errmsg', 'Form not submitted properly');

        }
    }

    /**
     * Add Sms Sender ID
     *
     * @param SmsSenderIdRequest $request
     * @return void
     */
    public function addRotationSenderId(SmsSenderIdRequest $request)
    {
        $formmode = ['ins','edt'];
        $frmmode = $request->frmmode;

        $url = $request->headers->get('referer');

        if (! in_array($frmmode, $formmode)) {
            return back()->with('errmsg','Form submission mode not specified');
        }

        switch($frmmode)
        {
            case "ins":
        
                if ($request->sendertype == 'general')
                {
                    
                    if (SmsSender::where('sender_name', $request->sender_name)->where('operator_id',NULL)->where('user',NULL)->exists()) {

                        return redirect($url)->with('errmsg','Senderid already exists');

                    }
                    
                    $i = 0;
                    $associate = [];
                    foreach($request->associate_sender_id as $associatesender) {
                        array_push($associate,[
                            'associate_sender_id' => str_replace(" ",'\"',$request->associate_sender_id[$i]),
                            'edit_associate_id' => $request->edit_associate_id[$i],
                            'associate_gateway' => $request->gateway[$i],
                            'status' => $request->sender_status[$i]
                        ]);

                        $i++;
                    }
                    $this->senderid->addSenderId([
                        'sendertype' => $request->sendertype,
                        'sender_name' => $request->sender_name,
                        'status' => $request->status,
                        'default' => $request->default,
                        'rotation_gateway_info' => json_encode($associate),
                        'created_by' => Auth::guard('root')->user()->id,
                        'updated_by' => Auth::guard('root')->user()->id
                    ]);

                    return redirect($url)->with('msg','Senderid successfully created');
                }

                if ($request->sendertype == 'teletalk'){

                    if (SmsSender::where('sender_name', $request->sender_name)->where('operator_id','!=',NULL)->where('user','!=',NULL)->exists()) {

                        return redirect($url)->with('errmsg','Senderid already exists');

                    }

                    $this->senderid->addSenderId([
                        'sendertype' => $request->sendertype,
                        'sender_name' => $request->sender_name,
                        'operator_id' => $request->sender_operator_id,
                        'status' => $request->status,
                        'default' => $request->default,
                        'user' => $request->sender_user,
                        'password' => $request->sender_password,
                        'created_by' => Auth::guard('root')->user()->id,
                        'updated_by' => Auth::guard('root')->user()->id
                    ]);

                    return redirect($url)->with('msg','Senderid successfully created');
                }
                break;
            case "edt":
                if (! $request->has('smssender_rec_id')) {
                    return back()->with('errmsg','ID missing');
                } 

                if(! $this->senderid->isValidSenderId($request->smssender_rec_id))
                {
                    return back()->with('errmsg','Record not found');
                }

                if ($request->sendertype == 'general')
                {
                    
                    $i = 0;
                    $associate = [];
                    foreach($request->associate_sender_id as $associatesender) {
                        array_push($associate,[
                            'associate_sender_id' => str_replace(" ",'\"',$request->associate_sender_id[$i]),
                            'edit_associate_id' => $request->edit_associate_id[$i],
                            'associate_gateway' => $request->gateway[$i],
                            'status' => $request->sender_status[$i]
                        ]);
        
                        $i++;
                    }
                    $this->senderid->updateSenderId([
                        'smssender_rec_id' => $request->smssender_rec_id,
                        'sendertype' => $request->sendertype,
                        'sender_name' => $request->sender_name,
                        'status' => $request->status,
                        'default' => $request->default,
                        'rotation_gateway_info' => json_encode($associate),
                        'created_by' => Auth::guard('root')->user()->id,
                        'updated_by' => Auth::guard('root')->user()->id
                    ]);
        
                    return redirect($url)->with('msg','Senderid successfully updated');
                }
        
                if ($request->sendertype == 'teletalk'){
                    $this->senderid->updateSenderId([
                        'smssender_rec_id' => $request->smssender_rec_id,
                        'sendertype' => $request->sendertype,
                        'sender_name' => $request->sender_name,
                        'operator_id' => $request->sender_operator_id,
                        'status' => $request->status,
                        'default' => $request->default,
                        'user' => $request->sender_user,
                        'password' => $request->sender_password,
                        'created_by' => Auth::guard('root')->user()->id,
                        'updated_by' => Auth::guard('root')->user()->id
                    ]);
        
                    return redirect($url)->with('msg','Senderid successfully updated');
                }
                break;

                default:
                    return back()->with('errmsg', 'Form not submitted properly');

        }
    }


    /**
     * Add Sms Sender ID
     *
     * @param SmsSenderIdRequest $request
     * @return void
     */
    public function addMultipleTemplateRotationSenderId(SmsSenderIdRequest $request)
    {
        $formmode = ['ins','edt'];
        $frmmode = $request->frmmode;

        $url = $request->headers->get('referer');

        if (! in_array($frmmode, $formmode)) {
            return back()->with('errmsg','Form submission mode not specified');
        }

        switch($frmmode)
        {
            case "ins":
        
                if ($request->sendertype == 'general')
                {
                    
                    if (SmsSender::where('sender_name', $request->sender_name)->where('operator_id',NULL)->where('user',NULL)->exists()) {

                        return redirect($url)->with('errmsg','Senderid already exists');

                    }
                    
                    $i = 0;
                    $associate = [];
                    foreach($request->associate_sender_id as $associatesender) {
                        array_push($associate,[
                            'associate_sender_id' => str_replace(" ",'\"',$request->associate_sender_id[$i]),
                            'edit_associate_id' => $request->edit_associate_id[$i],
                            'associate_gateway' => $request->gateway[$i],
                            'template' => $request->template[$i],
                            'status' => $request->sender_status[$i]
                        ]);

                        $i++;
                    }
                    $this->senderid->addSenderId([
                        'sendertype' => $request->sendertype,
                        'sender_name' => $request->sender_name,
                        'status' => $request->status,
                        'default' => $request->default,
                        'multiple_template_gateway_info' => json_encode($associate),
                        'created_by' => Auth::guard('root')->user()->id,
                        'updated_by' => Auth::guard('root')->user()->id
                    ]);

                    return redirect($url)->with('msg','Senderid successfully created');
                }

                if ($request->sendertype == 'teletalk'){

                    if (SmsSender::where('sender_name', $request->sender_name)->where('operator_id','!=',NULL)->where('user','!=',NULL)->exists()) {

                        return redirect($url)->with('errmsg','Senderid already exists');

                    }

                    $this->senderid->addSenderId([
                        'sendertype' => $request->sendertype,
                        'sender_name' => $request->sender_name,
                        'operator_id' => $request->sender_operator_id,
                        'status' => $request->status,
                        'default' => $request->default,
                        'user' => $request->sender_user,
                        'password' => $request->sender_password,
                        'created_by' => Auth::guard('root')->user()->id,
                        'updated_by' => Auth::guard('root')->user()->id
                    ]);

                    return redirect($url)->with('msg','Senderid successfully created');
                }
                break;
            case "edt":
                if (! $request->has('smssender_rec_id')) {
                    return back()->with('errmsg','ID missing');
                } 

                if(! $this->senderid->isValidSenderId($request->smssender_rec_id))
                {
                    return back()->with('errmsg','Record not found');
                }

                if ($request->sendertype == 'general')
                {
                    
                    $i = 0;
                    $associate = [];
                    foreach($request->associate_sender_id as $associatesender) {
                        array_push($associate,[
                            'associate_sender_id' => str_replace(" ",'\"',$request->associate_sender_id[$i]),
                            'edit_associate_id' => $request->edit_associate_id[$i],
                            'associate_gateway' => $request->gateway[$i],
                            'template' => $request->template[$i],
                            'status' => $request->sender_status[$i]
                        ]);
        
                        $i++;
                    }
                    $this->senderid->updateSenderId([
                        'smssender_rec_id' => $request->smssender_rec_id,
                        'sendertype' => $request->sendertype,
                        'sender_name' => $request->sender_name,
                        'status' => $request->status,
                        'default' => $request->default,
                        'multiple_template_gateway_info' => json_encode($associate),
                        'created_by' => Auth::guard('root')->user()->id,
                        'updated_by' => Auth::guard('root')->user()->id
                    ]);
        
                    return redirect($url)->with('msg','Senderid successfully updated');
                }
        
                if ($request->sendertype == 'teletalk'){
                    $this->senderid->updateSenderId([
                        'smssender_rec_id' => $request->smssender_rec_id,
                        'sendertype' => $request->sendertype,
                        'sender_name' => $request->sender_name,
                        'operator_id' => $request->sender_operator_id,
                        'status' => $request->status,
                        'default' => $request->default,
                        'user' => $request->sender_user,
                        'password' => $request->sender_password,
                        'created_by' => Auth::guard('root')->user()->id,
                        'updated_by' => Auth::guard('root')->user()->id
                    ]);
        
                    return redirect($url)->with('msg','Senderid successfully updated');
                }
                break;

                default:
                    return back()->with('errmsg', 'Form not submitted properly');

        }
    }


    /**
     * Update sms sender id
     *
     * @param SmsSenderIdRequest $request
     * @return void
     */
    public function updateSmsSenderId(SmsSenderIdRequest $request)
    {
        if (! $request->has('smssender_rec_id')) {
            return back()->with('errmsg','ID missing');
        } 
        if ($request->sendertype == 'general')
        {
            
            $i = 0;
            $associate = [];
            foreach($request->sender_operator_name as $operatorname) {
                array_push($associate,[
                    'sender_operator_name' => $operatorname,
                    'associate_sender_id' => str_replace(" ",'\"',$request->associate_sender_id[$i]),
                    'edit_associate_id' => $request->edit_associate_id[$i],
                    'associate_gateway' => $request->gateway[$i]
                ]);

                $i++;
            }
            $this->senderid->updateSenderId([
                'smssender_rec_id' => $request->smssender_rec_id,
                'sendertype' => $request->sendertype,
                'sender_name' => $request->sender_name,
                'status' => $request->status,
                'default' => $request->default,
                'gateway_info' => json_encode($associate),
                'created_by' => Auth::guard('root')->user()->id,
                'updated_by' => Auth::guard('root')->user()->id
            ]);

            return back();
        }

        if ($request->sendertype == 'teletalk'){
            $this->senderid->updateSenderId([
                'smssender_rec_id' => $request->smssender_rec_id,
                'sendertype' => $request->sendertype,
                'sender_name' => $request->sender_name,
                'operator_id' => $request->sender_operator_id,
                'status' => $request->status,
                'default' => $request->default,
                'user' => $request->sender_user,
                'password' => $request->sender_password,
                'created_by' => Auth::guard('root')->user()->id,
                'updated_by' => Auth::guard('root')->user()->id
            ]);

            return back();
        }
    }

    public function showSmsSenderId($senderidtype){

        $gateways = $this->gateway->getGateways();
        
        $operators = $this->operators->getOperators();

        // dd($operators);

        return view('smsview.smssenderid.sms-sender-id-list',compact('gateways','operators','senderidtype'));
    }


    public function showRotationSmsSenderId($senderidtype){

        $gateways = $this->gateway->getGateways();
        
        $operators = $this->operators->getOperators();
        return view('smsview.smssenderid.rotation-sms-senderid-list',compact('gateways','operators','senderidtype'));
    }

    public function showMultipleRotationSmsSenderId($senderidtype){

        $gateways = $this->gateway->getGateways();
        
        $operators = $this->operators->getOperators();

        $templates = TemplateResource::collection(Template::where('status',true)
                                                        ->where('user_type','root')
                                                        ->get());
                                                        
        return view('smsview.smssenderid.multiple-rotation-sms-senderid-list',compact('gateways','operators','senderidtype','templates'));
    }


    public function showSmsSenderIdTeletalk($senderidtype){

        $gateways = $this->gateway->getGateways();
        
        $operators = $this->operators->getOperators();
        return view('smsview.smssenderid.sms-sender-id-list-teletalk',compact('gateways','operators','senderidtype'));
    }

    public function showSenderIdForReseller($senderidtype){

        $gateways = $this->gateway->getGateways();
        
        $operators = $this->operators->getOperators();
        return view('smsview.smssenderid.show-senderid-for-reseller',compact('gateways','operators','senderidtype'));
    }

    public function showSmsSenderIdTeletalkForReseller($senderidtype){

        $gateways = $this->gateway->getGateways();
        
        $operators = $this->operators->getOperators();
        return view('smsview.smssenderid.reseller-sms-sender-id-list-teletalk',compact('gateways','operators','senderidtype'));
    }


    public function showSenderIdForResellerClient(){

        $gateways = $this->gateway->getGateways();
        
        $operators = $this->operators->getOperators();
        return view('smsview.smssenderid.show-senderid-for-reseller-client',compact('gateways','operators'));
    }


    public function renderSenderId($senderidtype)
    {
        return $this->senderid->showSmsSenderId($senderidtype);
    }

    public function showApiGateways()
    {
        return $this->gateway->showApiGateways();
    }
}
