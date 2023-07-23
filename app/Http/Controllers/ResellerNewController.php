<?php

namespace App\Http\Controllers;

use App\Core\AccountsChart\AccountsHead;
use App\Core\Countries\CountriesInterface;
use App\Core\Users\ResellerInterface;
use App\Core\ClientDocuments\ClientDocumentsInterface;
use Illuminate\Http\Request;
use App\Reseller;
use App\SenderidResellers;
use App\SenderidUsers;
use App\User;
use DB;
use App\Core\ProductSales\ProductSales;
use App\Core\ResellerDocuments\ResellerDocumentsInterface;
use App\Core\Senderid\SenderId;
use App\Core\UserCountSms\UserCountSms;
use App\Core\Users\ClientInterface;
use App\Http\Resources\SmsSenderIdResource;
use App\Core\BalanceReconciliation\BalanceReconciliation;
use App\SmsSender;

use Illuminate\Support\Facades\Auth;

class ResellerNewController extends Controller
{

    /**
     * Config settings
     */

    protected $config;

    /**
      * Client Document service
      *
      * @var App\Core\ClientDocuments\ClientDocumentUpload
      */
      protected $clientDocument;

    /**
      * Client Service
      *
      * @var App\Core\Users\ClientRepository
      */
      protected $client;

    /**
      * Reseller service
      *
      * @var App\Core\Users\ResellerRepository
      */
      protected $reseller;

      /**
       * Product sale service
       *
       * @var App\Core\ProductSales\ProductSaleDetails
       */
      protected $productsale;
 
      /**
       * Client sms sent service
       *
       * @var App\Core\UserCountSms\UserCountSmsDetails
       */
      protected $smssent;

      /**
     * Country service
     *
     * @var Object App\Core\Countries\Countries
     */
    protected $country;

    /**
      * reseller Document service
      *
      * @var App\Core\ClientDocuments\resellerDocumentUpload
      */
      protected $resellerDocument;


      /**
     * Sender ID Service
     *
     * @var App\Core\Semderid\SenderidDetails
     */
    protected $senderid;

    /**
     * Undocumented variable
     *
     * @var App\Core\AccountsChart\AccountsHeadDetails
     */
    public $accountshead;

    public $userbalance;


    public function __construct(
        SenderId $senderid,
        ResellerInterface $reseller,
        CountriesInterface $country,
        ClientInterface $client,
        ProductSales $productsale,
        ClientDocumentsInterface $clientDocument,
        ResellerDocumentsInterface $resellerDocument,
        UserCountSms $smssent,
        AccountsHead $accountshead,
        BalanceReconciliation $userbalance

    )
    {
        
        $this->middleware('auth:reseller,root,manager');

        $this->reseller = $reseller;

        $this->country = $country;

        $this->client = $client;

        $this->productsale = $productsale;

        $this->smssent = $smssent;

        $this->clientDocument = $clientDocument;

        $this->resellerDocument = $resellerDocument;

        $this->senderid = $senderid;

        $this->accountshead = $accountshead;

        $this->userbalance = $userbalance;
    }

    /**
     * Reseller dashboard
     *
     * @return void
     */
    public function reseller()
    {

        $totalusers = $this->client->resellerTotalUsers(['reseller_id' => Auth::guard('reseller')->user()->id]);

        $totdaysproductsale = $this->productsale->resellerTotalSalesInToday(['invoice_owner_id' => Auth::guard('reseller')->user()->id]);

        $totalproductsalebyroot = $this->productsale->resellerTotalSalesByRoot(['invoice_owner_id' => Auth::guard('reseller')->user()->id]);

        $totalrevinue = $this->productsale->resellerTotalRevinueInTodayByRoot(['invoice_owner_id' => Auth::guard('reseller')->user()->id]);
        
        $totalrevinueinmonth = $this->productsale->resellerTotalRevinueInCurrentMonthByRoot(['invoice_owner_id' => Auth::guard('reseller')->user()->id]);
        
        $totalrevinueinyear = $this->productsale->resellerTotalRevinueInCurrentYearByRoot(['invoice_owner_id' => Auth::guard('reseller')->user()->id]);

        $todayssmssent = $this->smssent->resellerClientTodaysSmsSentHistoryForRoot(['owner_id' => Auth::guard('reseller')->user()->id]);
        
        //$thisweekssmssent = $this->smssent->resellerThisWeekSmsSentHistoryForRoot(['owner_id' => Auth::guard('reseller')->user()->id]);
        
        //$thismonthsmssent = $this->smssent->resellerThisMonthSmsSentHistoryForRoot(['owner_id' => Auth::guard('reseller')->user()->id]);

        $todaysenrollclient = $this->client->todaysResellerEnrollClientForRoot(['reseller_id' => Auth::guard('reseller')->user()->id]);
        
        $monthlyenrollclient = $this->client->resellerMonthlyEnrollClientForRoot(['reseller_id' => Auth::guard('reseller')->user()->id]);


        $totdaymasksentbal = $this->smssent->resellerClientConsumeMaskSmsBalance(Auth::guard('reseller')->user()->id);
        $todaynonmasksentbal = $this->smssent->resellerClientConsumeNonMaskSmsBalance(Auth::guard('reseller')->user()->id);
        $todayvoicesentbal = $this->smssent->resellerClientConsumeVoiceSmsBalance(Auth::guard('reseller')->user()->id);

        $totalsentsms = ($totdaymasksentbal+$todaynonmasksentbal+$todayvoicesentbal);
        
        //$thisweeksentsmsbal = $this->smssent->resellerClientThisWeekConsumeMaskSmsBalance(Auth::guard('reseller')->user()->id);
        //$thismonthsentsmsbal = $this->smssent->resellerClientThisMonthConsumeMaskSmsBalance(Auth::guard('reseller')->user()->id);


        $clientmasksmsbal = $this->productsale->getResellerSmsBalanceByCategory(Auth::guard('reseller')->user()->id,'mask');
        $clientnonmasksmsbal = $this->productsale->getResellerSmsBalanceByCategory(Auth::guard('reseller')->user()->id,'nomask');
        $clientvoicemsbal = $this->productsale->getResellerSmsBalanceByCategory(Auth::guard('reseller')->user()->id,'voice');

        $totalmaskbal = ($clientmasksmsbal-$this->smssent->resellerTotalConsumeMaskBalance(Auth::guard('reseller')->user()->id));
        $totalnonmaskbal = ($clientnonmasksmsbal-$this->smssent->resellerTotalConsumeNonMaskBalance(Auth::guard('reseller')->user()->id));
        $totalvoicebal = ($clientvoicemsbal-$this->smssent->resellerTotalConsumeVoiceBalance(Auth::guard('reseller')->user()->id));
        
        return view('smsview.reseller.index',compact(
            'totalusers',
            'totdaysproductsale',
            'totalproductsalebyroot',
            'totalrevinue',
            'totalrevinueinmonth',
            'totalrevinueinyear',
            'todayssmssent',
            //'thisweekssmssent',
            //'thismonthsmssent',
            'todaysenrollclient',
            'monthlyenrollclient',
            //'totalsentsms',
            //'thisweeksentsmsbal',
            //'thismonthsentsmsbal',
            'totalmaskbal',
            'totalnonmaskbal',
            'totalvoicebal'
        ));
    }


    public function resellerBalance(Request $request)
    {

        if ($request->userid) {
            $reseller = Reseller::find($request->userid);
        } else {
            $reseller = Auth::guard('reseller')->user();
        }

        return response()->json(['balance' => [
            'maskbalance' => $reseller->mask_balance,
            'nonmaskbalance' => $reseller->nonmask_balance,
            'voicebalance' => $reseller->voice_balance
        ]], 200);
    }


    /**
     * Root Client edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function resellerClientEdit(Request $request)
    {   
        $countries = $this->country
                        ->show();

        $clientDocuments = $this->clientDocument
                                ->showUserDocuments($request);

        $user = User::where('id', $request->userid)->first();
        
        return view('smsview.reseller.reseller-client-edit',compact('user','countries','request','clientDocuments'));
    }

    public function clientBalance(Request $request)
    {
        
        $clientid = !empty($request->userid) ? $request->userid : Auth::guard('web')->user()->id;
        $totdaymasksentbal = $this->smssent->clientConsumeMaskSmsBalance($clientid);
        $todaynonmasksentbal = $this->smssent->clientConsumeNonMaskSmsBalance($clientid);
        $todayvoicesentbal = $this->smssent->clientConsumeVoiceSmsBalance($clientid);

        $totalsentsms = ($totdaymasksentbal+$todaynonmasksentbal+$todayvoicesentbal);

        $thisweeksentsmsbal = $this->smssent->clientThisWeekConsumeMaskSmsBalance($clientid);
        $thismonthsentsmsbal = $this->smssent->clientThisMonthConsumeMaskSmsBalance($clientid);


        $clientmasksmsbal = $this->productsale->getSmsBalanceByCategory($clientid,'mask');
        $clientnonmasksmsbal = $this->productsale->getSmsBalanceByCategory($clientid,'nomask');
        $clientvoicemsbal = $this->productsale->getSmsBalanceByCategory($clientid,'voice');

        $totalmaskbal = ($clientmasksmsbal-$this->smssent->totalConsumeMaskBalance($clientid));
        $totalnonmaskbal = ($clientnonmasksmsbal-$this->smssent->totalConsumeNonMaskBalance($clientid));
        $totalvoicebal = ($clientvoicemsbal-$this->smssent->totalConsumeVoiceBalance($clientid));

        return response()->json(['balance' => [
            'maskbalance' => $totalmaskbal,
            'nonmaskbalance' => $totalnonmaskbal,
            'voicebalance' => $totalvoicebal
        ]], 200);
    }


    /**
     * Root Client edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function resellerClientProfileSenderid(Request $request)
    {

        $countries = $this->country
                        ->show();

        $clientDocuments = $this->clientDocument
                                ->showUserDocuments($request);

        $user = User::where('id', $request->userid)->first();

        $totalmaskbal = $user->mask_balance;
        $totalnonmaskbal = $user->nonmask_balance;
        $totalvoicebal = $user->voice_balance;

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

        $resellerid = Auth::guard('reseller')->user()->id;

        $senderids = SenderidResellers::whereNotIn('senderid', function($query) use($userid){
                $query->select('senderid')
                      ->from('senderid_users')
                      ->where('user',$userid);
            })->where('reseller', $resellerid)->get();

        $assignedSenderIds = SenderidUsers::where('user', $userid)->get();

        return view('smsview.reseller.client-profile-senderid',compact('user','countries','request','clientDocuments','totalmaskbal','totalnonmaskbal','totalvoicebal','senderids', 'assignedSenderIds'));
    }


    public function assignSenderIdToClientByReseller($userid, Request $request)
    {
        if (is_array($request->resellerSenderid)) {

            $owner = Auth::guard('reseller')->user();
            $usertype = 'reseller';

            $client = User::find($userid);
            if (!$client) {
                return response()->json(['errmsg' => 'Invalid request'], 406);
            }

            $resellerSenderid = $request->resellerSenderid;

            foreach($resellerSenderid as $senderid) {
                $senderidUser = SenderidUsers::create([
                    'senderid'      => $senderid,
                    'user'          => $client->id,
                    'user_type'     => $usertype,
                    'created_by'    => $owner->id,
                    'updated_by'    => $owner->id,
                    'active'        => 1,
                ]);
                
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
    public function deleteClientSenderId($user, $senderid)
    {

        if (!Auth::guard('reseller')->check())
        {
            return response()->json(['errmsg' => 'Invalid request'], 406);
            
        }

        $senderidUserData = SenderidUsers::where('senderid', $senderid)
        ->where('user', $user)
        ->first();

        if ($senderidUserData)
        {            
            $senderidUserData->delete();

            return response()->json(['msg' => 'Senderid deleted successfully'],200);
        }

        return response()->json(['errmsg' => 'Senderid not found or invalid request'], 406);


    }

}
