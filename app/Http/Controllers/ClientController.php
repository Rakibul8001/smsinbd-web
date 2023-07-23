<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Core\Users\ClientInterface;
use App\Core\Countries\CountriesInterface;
use App\Core\ClientDocuments\ClientDocumentsInterface;
use App\Core\BalanceReconciliation\BalanceReconciliation;
use App\Core\ProductSales\ProductSales;
use App\Core\UserCountSms\UserCountSms;
use App\Http\Resources\SmsSenderIdResource;
use App\Core\AccountsChart\AccountsHead;
use App\SmsSender;
use Illuminate\Support\Facades\Auth;
use DB;

class ClientController extends Controller
{

    /**
     * Country service
     *
     * @var App\Core\Countries\Countries
     */
    protected $country;

    /**
     * Client Document Service
     *
     * @var App\Core\ClientDocuments\ClientDocumentsInterface
     */
    protected $clientDocument;

    protected $consumesms;

    protected $product;

    public $userbalance;

    public $accountshead;

    public function __construct(
        CountriesInterface $country,
        ClientDocumentsInterface $clientDocument,
        UserCountSms $consumesms,
        ProductSales $product,
        BalanceReconciliation $userbalance,
        AccountsHead $accountshead
    )
    {
        $this->middleware('auth:web');

        $this->country = $country;

        $this->clientDocument = $clientDocument;

        $this->consumesms = $consumesms;

        $this->product = $product;

        $this->userbalance = $userbalance;

        $this->accountshead = $accountshead;

        //$this->client = $client;

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');
    }
    
    /**
     * Client dashboard
     *
     * @return void
     */
    public function client()
    {
        ini_set('memory_limit', '264M');
        
        $clientid = Auth::guard('web')->user();

        //$this->userbalance->calculateUserBalance($clientid->id);
        $totdaymasksentbal = $this->consumesms->clientConsumeMaskSmsBalance($clientid->id);
        $todaynonmasksentbal = $this->consumesms->clientConsumeNonMaskSmsBalance($clientid->id);
        $todayvoicesentbal = $this->consumesms->clientConsumeVoiceSmsBalance($clientid->id);

        $totalsentsms = ($totdaymasksentbal+$todaynonmasksentbal+$todayvoicesentbal);

        $thisweeksentsmsbal = $this->consumesms->clientThisWeekConsumeMaskSmsBalance($clientid->id);
        $thismonthsentsmsbal = $this->consumesms->clientThisMonthConsumeMaskSmsBalance($clientid->id);


        $clientmasksmsbal = $this->product->getSmsBalanceByCategory($clientid->id,'mask');
        $clientnonmasksmsbal = $this->product->getSmsBalanceByCategory($clientid->id,'nomask');
        $clientvoicemsbal = $this->product->getSmsBalanceByCategory($clientid->id,'voice');

        $totalmaskbal = ($clientmasksmsbal-$this->consumesms->totalConsumeMaskBalance($clientid->id));
        $totalnonmaskbal = ($clientnonmasksmsbal-$this->consumesms->totalConsumeNonMaskBalance($clientid->id));
        $totalvoicebal = ($clientvoicemsbal-$this->consumesms->totalConsumeVoiceBalance($clientid->id));

        $totalPurchasedSms = DB::select("select SUM(credit-debit) AS total_purchased, type  from account_transactions where user =$clientid->id AND note IN ('Payment Invoice Created', 'Return Invoice Created') GROUP BY type");

        $totalCampaignSms = DB::select("SELECT SUM(sms_qty*total_numbers) total_sent, category as type from sms_campaigns where user_id=$clientid->id  and active=1 GROUP BY category");

        $totalSingleSms = DB::select("SELECT SUM(qty) total_sent, category as type from sms_individuals where user_id=$clientid->id  and active=1 GROUP BY category");

        $maskPurchase = $nonmaskPurchase = $maskSent = $nonmaskSent = 0;
        foreach ($totalPurchasedSms as $key => $data) {
            if($data->type==1){
                $maskPurchase = $data->total_purchased;
            }
            if($data->type==2){
                $nonmaskPurchase = $data->total_purchased;
            }
        }

        foreach ($totalCampaignSms as $key => $data) {
            if($data->type==1){
                $maskSent = $data->total_sent;
            }
            if($data->type==2){
                $nonmaskSent = $data->total_sent;
            }
        }
        foreach ($totalSingleSms as $key => $data) {
            if($data->type==1){
                $maskSent += $data->total_sent;
            }
            if($data->type==2){
                $nonmaskSent += $data->total_sent;
            }
        }

        $totalData['maskPurchase'] = $maskPurchase;
        $totalData['nonmaskPurchase'] = $nonmaskPurchase;
        $totalData['maskSent'] = $maskSent;
        $totalData['nonmaskSent'] = $nonmaskSent;

        session()->put('totalmaskbal', $totalmaskbal);
        session()->put('totalnonmaskbal', $totalnonmaskbal);
        session()->put('totalvoicebal', $totalvoicebal);

        return view('smsview.client.index',compact(
            'totalsentsms',
            'thisweeksentsmsbal',
            'thismonthsentsmsbal',
            'totalmaskbal',
            'totalnonmaskbal',
            'totalvoicebal',
            'totalData')
        );
    }

    /**
     * Client edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function clientEdit(Request $request)
    { 
        $clientid = Auth::guard('web')->user();
        if($request->userid!=$clientid->id)
            return redirect()->route('client');
            
        $countries = $this->country
                        ->show();

        $clientDocuments = $this->clientDocument
                                ->showUserDocuments($request);
                                
        $user = User::where('id',$request->userid)->first();
        
        return view('smsview.client.client-edit',compact(
            'user',
            'countries',
            'clientDocuments'
        ));
    }

    /**
     * Root Client edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function rootClientProfile(Request $request)
    {   
        $clientid = Auth::guard('web')->user();
        if($request->userid!=$clientid->id)
            return redirect()->route('client');
            
        $this->userbalance->calculateUserBalance($request->userid);
        $countries = $this->country
                        ->show();

        
        if (User::where('id', $request->userid)->exists()) {

            $user = User::where('id', $request->userid)->first();

            $clientmasksmsbal = $this->product->getSmsBalanceByCategory($request->userid,'mask');
            $clientnonmasksmsbal = $this->product->getSmsBalanceByCategory($request->userid,'nomask');
            $clientvoicemsbal = $this->product->getSmsBalanceByCategory($request->userid,'voice');

            $totalmaskbal = ($clientmasksmsbal-$this->consumesms->totalConsumeMaskBalance($request->userid));
            $totalnonmaskbal = ($clientnonmasksmsbal-$this->consumesms->totalConsumeNonMaskBalance($request->userid));
            $totalvoicebal = ($clientvoicemsbal-$this->consumesms->totalConsumeVoiceBalance($request->userid));

            
            
            return view('smsview.client.root-client-profile',compact('user','countries','request','totalmaskbal','totalnonmaskbal','totalvoicebal'));
        } else {
            return view('smsview.pagenotfound.404');
        }    
    }

    /**
     * Root Client edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function rootClientProfileDocument(Request $request)
    {   
        $clientid = Auth::guard('web')->user();
        if($request->userid!=$clientid->id)
            return redirect()->route('client');
            
        $this->userbalance->calculateUserBalance($request->userid);
        $countries = $this->country
                        ->show();

        $clientDocuments = $this->clientDocument
                                ->showUserDocuments($request);

        $user = User::where('id', $request->userid)->first();

        $clientmasksmsbal = $this->product->getSmsBalanceByCategory($request->userid,'mask');
        $clientnonmasksmsbal = $this->product->getSmsBalanceByCategory($request->userid,'nomask');
        $clientvoicemsbal = $this->product->getSmsBalanceByCategory($request->userid,'voice');

        $totalmaskbal = ($clientmasksmsbal-$this->consumesms->totalConsumeMaskBalance($request->userid));
        $totalnonmaskbal = ($clientnonmasksmsbal-$this->consumesms->totalConsumeNonMaskBalance($request->userid));
        $totalvoicebal = ($clientvoicemsbal-$this->consumesms->totalConsumeVoiceBalance($request->userid));
        
        return view('smsview.client.client-profile-document',compact('user','countries','request','clientDocuments','totalmaskbal','totalnonmaskbal','totalvoicebal'));
    }


    /**
     * Root Client edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function rootClientProfileIndex(Request $request)
    {   
        $clientid = Auth::guard('web')->user();
        if($request->userid!=$clientid->id)
            return redirect()->route('client');
            
        $this->userbalance->calculateUserBalance($request->userid);
        $countries = $this->country
                        ->show();

        $clientDocuments = $this->clientDocument
                                ->showUserDocuments($request);

        $user = User::where('id', $request->userid)->first();

        $totdaymasksentbal = $this->consumesms->clientConsumeMaskSmsBalance($request->userid);
        $todaynonmasksentbal = $this->consumesms->clientConsumeNonMaskSmsBalance($request->userid);
        $todayvoicesentbal = $this->consumesms->clientConsumeVoiceSmsBalance($request->userid);

        $totalsentsms = ($totdaymasksentbal+$todaynonmasksentbal+$todayvoicesentbal);

        $thisweeksentsmsbal = $this->consumesms->clientThisWeekConsumeMaskSmsBalance($request->userid);
        $thismonthsentsmsbal = $this->consumesms->clientThisMonthConsumeMaskSmsBalance($request->userid);

        $clientmasksmsbal = $this->product->getSmsBalanceByCategory($request->userid,'mask');
        $clientnonmasksmsbal = $this->product->getSmsBalanceByCategory($request->userid,'nomask');
        $clientvoicemsbal = $this->product->getSmsBalanceByCategory($request->userid,'voice');

        $totalmaskbal = ($clientmasksmsbal-$this->consumesms->totalConsumeMaskBalance($request->userid));
        $totalnonmaskbal = ($clientnonmasksmsbal-$this->consumesms->totalConsumeNonMaskBalance($request->userid));
        $totalvoicebal = ($clientvoicemsbal-$this->consumesms->totalConsumeVoiceBalance($request->userid));

        
        
        return view('smsview.client.client-profile-index',compact('totalsentsms','thisweeksentsmsbal','thismonthsentsmsbal','user','countries','request','clientDocuments','totalmaskbal','totalnonmaskbal','totalvoicebal'));
    }


    public function clientTotalMonthlySms(Request $request) {
        $clientid = Auth::guard('web')->user();
        if($request->userid!=$clientid->id)
            return redirect()->route('client');
            
        $montlysmses = DB::select(DB::raw("
                select user_id,sum(number_of_sms) totalsms,monthname(submitted_at) month,year(submitted_at) year
                from archive_sent_smses
                where user_id=$request->userid
                and status=true
                group by monthname(submitted_at)
                order by submitted_at 
        "));

        $smsarray = [];

        foreach($montlysmses as $sms) {
            
            $smsarray['totalsms'][] = [$sms->month,(int)$sms->totalsms];
        }

        return json_encode($smsarray);
    }

    /**
     * Root Client edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function rootClientProfileSenderid(Request $request)
    {   
        $clientid = Auth::guard('web')->user();
        if($request->userid!=$clientid->id)
            return redirect()->route('client');
            
        $this->userbalance->calculateUserBalance($request->userid);

        $countries = $this->country
                        ->show();

        $clientDocuments = $this->clientDocument
                                ->showUserDocuments($request);

        $user = User::where('id', $request->userid)->first();

        $clientmasksmsbal = $this->product->getSmsBalanceByCategory($request->userid,'mask');
        $clientnonmasksmsbal = $this->product->getSmsBalanceByCategory($request->userid,'nomask');
        $clientvoicemsbal = $this->product->getSmsBalanceByCategory($request->userid,'voice');

        $totalmaskbal = ($clientmasksmsbal-$this->consumesms->totalConsumeMaskBalance($request->userid));
        $totalnonmaskbal = ($clientnonmasksmsbal-$this->consumesms->totalConsumeNonMaskBalance($request->userid));
        $totalvoicebal = ($clientvoicemsbal-$this->consumesms->totalConsumeVoiceBalance($request->userid));

        $userid = $request->userid;

        $senderids = SmsSenderIdResource::collection(
            SmsSender::whereNotIn('id', function($query) use($userid){
                $query->select('sms_sender_id')
                      ->from('user_senders')
                      ->where('user_id',$userid);
            })
            ->where('operator_id','!=',NULL)
            ->get()
        );

        
        return view('smsview.client.client-profile-senderid',compact('user','countries','request','clientDocuments','totalmaskbal','totalnonmaskbal','totalvoicebal','senderids'));
    }


    /**
     * Root Client edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function rootClientProfileTemplate(Request $request)
    {   
        $clientid = Auth::guard('web')->user();
        if($request->userid!=$clientid->id)
            return redirect()->route('client');
            
        $this->userbalance->calculateUserBalance($request->userid);
        $countries = $this->country
                        ->show();

        $clientDocuments = $this->clientDocument
                                ->showUserDocuments($request);

        $user = User::where('id', $request->userid)->first();

        $clientmasksmsbal = $this->product->getSmsBalanceByCategory($request->userid,'mask');
        $clientnonmasksmsbal = $this->product->getSmsBalanceByCategory($request->userid,'nomask');
        $clientvoicemsbal = $this->product->getSmsBalanceByCategory($request->userid,'voice');

        $totalmaskbal = ($clientmasksmsbal-$this->consumesms->totalConsumeMaskBalance($request->userid));
        $totalnonmaskbal = ($clientnonmasksmsbal-$this->consumesms->totalConsumeNonMaskBalance($request->userid));
        $totalvoicebal = ($clientvoicemsbal-$this->consumesms->totalConsumeVoiceBalance($request->userid));
        
        return view('smsview.client.client-profile-template',compact('user','countries','request','clientDocuments','totalmaskbal','totalnonmaskbal','totalvoicebal'));
    }

    /**
     * Root Client edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function rootClientProfileManageInvoice(Request $request)
    {   
        $clientid = Auth::guard('web')->user();
        if($request->userid!=$clientid->id)
            return redirect()->route('client');
            
        $countries = $this->country
                        ->show();

        $this->userbalance->calculateUserBalance($request->userid);

        $clientDocuments = $this->clientDocument
                                ->showUserDocuments($request);

        $user = User::where('id', $request->userid)->first();

        $clientmasksmsbal = $this->product->getSmsBalanceByCategory($request->userid,'mask');
        $clientnonmasksmsbal = $this->product->getSmsBalanceByCategory($request->userid,'nomask');
        $clientvoicemsbal = $this->product->getSmsBalanceByCategory($request->userid,'voice');

        $totalmaskbal = ($clientmasksmsbal-$this->consumesms->totalConsumeMaskBalance($request->userid));
        $totalnonmaskbal = ($clientnonmasksmsbal-$this->consumesms->totalConsumeNonMaskBalance($request->userid));
        $totalvoicebal = ($clientvoicemsbal-$this->consumesms->totalConsumeVoiceBalance($request->userid));

        $groups = $this->accountshead->getAllGroupAccountsHeadById(5);
        
        return view('smsview.client.client-profile-manage-invoice',compact('user','countries','request','clientDocuments','totalmaskbal','totalnonmaskbal','totalvoicebal','groups'));
    }

    public function clientBalance(Request $request)
    {
        $clientid = Auth::guard('web')->user();
        if($request->userid!=$clientid->id)
            return redirect()->route('client');
        
        $clientid = !empty($request->userid) ? $request->userid : Auth::guard('web')->user()->id;
        $totdaymasksentbal = $this->consumesms->clientConsumeMaskSmsBalance($clientid);
        $todaynonmasksentbal = $this->consumesms->clientConsumeNonMaskSmsBalance($clientid);
        $todayvoicesentbal = $this->consumesms->clientConsumeVoiceSmsBalance($clientid);

        $totalsentsms = ($totdaymasksentbal+$todaynonmasksentbal+$todayvoicesentbal);

        $thisweeksentsmsbal = $this->consumesms->clientThisWeekConsumeMaskSmsBalance($clientid);
        $thismonthsentsmsbal = $this->consumesms->clientThisMonthConsumeMaskSmsBalance($clientid);


        $clientmasksmsbal = $this->product->getSmsBalanceByCategory($clientid,'mask');
        $clientnonmasksmsbal = $this->product->getSmsBalanceByCategory($clientid,'nomask');
        $clientvoicemsbal = $this->product->getSmsBalanceByCategory($clientid,'voice');

        $totalmaskbal = ($clientmasksmsbal-$this->consumesms->totalConsumeMaskBalance($clientid));
        $totalnonmaskbal = ($clientnonmasksmsbal-$this->consumesms->totalConsumeNonMaskBalance($clientid));
        $totalvoicebal = ($clientvoicemsbal-$this->consumesms->totalConsumeVoiceBalance($clientid));

        return response()->json(['balance' => [
            'maskbalance' => $totalmaskbal,
            'nonmaskbalance' => $totalnonmaskbal,
            'voicebalance' => $totalvoicebal
        ]], 200);
    }

    public function getClientBalance(Request $request)
    {
        $clientid = Auth::guard('web')->user();
        if($request->userid!=$clientid->id)
            return redirect()->route('client');
            
        if ($request->userid) {
            $user = User::find($request->userid);
        } else {
            $user = Auth::guard('web')->user();
        }

        return response()->json(['balance' => [
            'maskbalance' => $user->mask_balance,
            'nonmaskbalance' => $user->nonmask_balance,
            'voicebalance' => $user->voice_balance
        ]], 200);
    }

}
