<?php

namespace App\Http\Controllers;

use App\Core\AccountsChart\AccountsHead;
use App\Core\Countries\CountriesInterface;
use App\Core\Users\ResellerInterface;
use App\Core\ClientDocuments\ClientDocumentsInterface;
use Illuminate\Http\Request;
use App\Reseller;
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

class ResellerController extends Controller
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
        $reseller = Auth::guard('reseller')->user();

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



        $totalmaskbal = $reseller->mask_balance;
        $totalnonmaskbal = $reseller->nonmask_balance;
        $totalvoicebal = $reseller->voice_balance;
        
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


    /**
     * Reseller client profile
     *
     * @param Request $request
     * @return void
     */
    public function resellerProfileIndex(Request $request) {

        if (Reseller::where('id',$request->userid)->exists()) {

            $user = Reseller::where('id', $request->userid)->first();

            $totalusers = $this->client->resellerTotalUsers(['reseller_id' => $request->userid]);

            $totdaysproductsale = $this->productsale->resellerTotalSalesInToday(['invoice_owner_id' => $request->userid]);

            $totalproductsalebyroot = $this->productsale->resellerTotalSalesByRoot(['invoice_owner_id' => $request->userid]);

            $totalrevinue = $this->productsale->resellerTotalRevinueInTodayByRoot(['invoice_owner_id' => $request->userid]);
            
            $totalrevinueinmonth = $this->productsale->resellerTotalRevinueInCurrentMonthByRoot(['invoice_owner_id' => $request->userid]);
            
            $totalrevinueinyear = $this->productsale->resellerTotalRevinueInCurrentYearByRoot(['invoice_owner_id' => $request->userid]);

            $todayssmssent = $this->smssent->resellerClientTodaysSmsSentHistoryForRoot(['owner_id' => $request->userid]);
            
            $thisweekssmssent = $this->smssent->resellerThisWeekSmsSentHistoryForRoot(['owner_id' => $request->userid]);
            
            $thismonthsmssent = $this->smssent->resellerThisMonthSmsSentHistoryForRoot(['owner_id' => $request->userid]);

            $todaysenrollclient = $this->client->todaysResellerEnrollClientForRoot(['reseller_id' => $request->userid]);
            
            $monthlyenrollclient = $this->client->resellerMonthlyEnrollClientForRoot(['reseller_id' => $request->userid]);


            $totdaymasksentbal = $this->smssent->resellerClientConsumeMaskSmsBalance($request->userid);
            $todaynonmasksentbal = $this->smssent->resellerClientConsumeNonMaskSmsBalance($request->userid);
            $todayvoicesentbal = $this->smssent->resellerClientConsumeVoiceSmsBalance($request->userid);

            $totalsentsms = ($totdaymasksentbal+$todaynonmasksentbal+$todayvoicesentbal);
            
            //$thisweeksentsmsbal = $this->smssent->resellerClientThisWeekConsumeMaskSmsBalance(Auth::guard('reseller')->user()->id);
            //$thismonthsentsmsbal = $this->smssent->resellerClientThisMonthConsumeMaskSmsBalance(Auth::guard('reseller')->user()->id);


            $clientmasksmsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'mask');
            $clientnonmasksmsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'nomask');
            $clientvoicemsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'voice');

            $totalmaskbal = ($clientmasksmsbal-$this->smssent->resellerTotalConsumeMaskBalance($request->userid));
            $totalnonmaskbal = ($clientnonmasksmsbal-$this->smssent->resellerTotalConsumeNonMaskBalance($request->userid));
            $totalvoicebal = ($clientvoicemsbal-$this->smssent->resellerTotalConsumeVoiceBalance($request->userid));
            
            return view('smsview.reseller.reseller-profile-index',compact(
                'user',
                'totalusers',
                'totdaysproductsale',
                'totalproductsalebyroot',
                'totalrevinue',
                'totalrevinueinmonth',
                'totalrevinueinyear',
                'todayssmssent',
                'thisweekssmssent',
                'thismonthsmssent',
                'todaysenrollclient',
                'monthlyenrollclient',
                //'totalsentsms',
                //'thisweeksentsmsbal',
                //'thismonthsentsmsbal',
                'totalmaskbal',
                'totalnonmaskbal',
                'totalvoicebal',
                'request'
            ));
        } else {
            return view('smsview.pagenotfound.404');
        }   
    }


    /**
     * Reseller client profile
     *
     * @param Request $request
     * @return void
     */
    public function resellerProfile(Request $request) {

        if (Reseller::where('id',$request->userid)->exists()) {

            $countries = $this->country
                        ->show();

            $clientDocuments = $this->resellerDocument
                                ->showUserDocuments($request);

            $user = Reseller::where('id', $request->userid)->first();

            $totalusers = $this->client->resellerTotalUsers(['reseller_id' => $request->userid]);

            $totdaysproductsale = $this->productsale->resellerTotalSalesInToday(['invoice_owner_id' => $request->userid]);

            $totalproductsalebyroot = $this->productsale->resellerTotalSalesByRoot(['invoice_owner_id' => $request->userid]);

            $totalrevinue = $this->productsale->resellerTotalRevinueInTodayByRoot(['invoice_owner_id' => $request->userid]);
            
            $totalrevinueinmonth = $this->productsale->resellerTotalRevinueInCurrentMonthByRoot(['invoice_owner_id' => $request->userid]);
            
            $totalrevinueinyear = $this->productsale->resellerTotalRevinueInCurrentYearByRoot(['invoice_owner_id' => $request->userid]);

            $todayssmssent = $this->smssent->resellerClientTodaysSmsSentHistoryForRoot(['owner_id' => $request->userid]);
            
            $thisweekssmssent = $this->smssent->resellerThisWeekSmsSentHistoryForRoot(['owner_id' => $request->userid]);
            
            $thismonthsmssent = $this->smssent->resellerThisMonthSmsSentHistoryForRoot(['owner_id' => $request->userid]);

            $todaysenrollclient = $this->client->todaysResellerEnrollClientForRoot(['reseller_id' => $request->userid]);
            
            $monthlyenrollclient = $this->client->resellerMonthlyEnrollClientForRoot(['reseller_id' => $request->userid]);


            $totdaymasksentbal = $this->smssent->resellerClientConsumeMaskSmsBalance($request->userid);
            $todaynonmasksentbal = $this->smssent->resellerClientConsumeNonMaskSmsBalance($request->userid);
            $todayvoicesentbal = $this->smssent->resellerClientConsumeVoiceSmsBalance($request->userid);

            $totalsentsms = ($totdaymasksentbal+$todaynonmasksentbal+$todayvoicesentbal);
            
            //$thisweeksentsmsbal = $this->smssent->resellerClientThisWeekConsumeMaskSmsBalance(Auth::guard('reseller')->user()->id);
            //$thismonthsentsmsbal = $this->smssent->resellerClientThisMonthConsumeMaskSmsBalance(Auth::guard('reseller')->user()->id);


            $clientmasksmsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'mask');
            $clientnonmasksmsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'nomask');
            $clientvoicemsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'voice');

            $totalmaskbal = ($clientmasksmsbal-$this->smssent->resellerTotalConsumeMaskBalance($request->userid));
            $totalnonmaskbal = ($clientnonmasksmsbal-$this->smssent->resellerTotalConsumeNonMaskBalance($request->userid));
            $totalvoicebal = ($clientvoicemsbal-$this->smssent->resellerTotalConsumeVoiceBalance($request->userid));
            
            return view('smsview.reseller.reseller-profile',compact(
                'user',
                'totalusers',
                'totdaysproductsale',
                'totalproductsalebyroot',
                'totalrevinue',
                'totalrevinueinmonth',
                'totalrevinueinyear',
                'todayssmssent',
                'thisweekssmssent',
                'thismonthsmssent',
                'todaysenrollclient',
                'monthlyenrollclient',
                //'totalsentsms',
                //'thisweeksentsmsbal',
                //'thismonthsentsmsbal',
                'totalmaskbal',
                'totalnonmaskbal',
                'totalvoicebal',
                'request',
                'clientDocuments',
                'countries'
            ));
        } else {
            return view('smsview.pagenotfound.404');
        }   
    }


    /**
     * Reseller client profile
     *
     * @param Request $request
     * @return void
     */
    public function resellerProfileDocumentUpload(Request $request) {

        if (Reseller::where('id',$request->userid)->exists()) {

            $countries = $this->country
                        ->show();

            $clientDocuments = $this->resellerDocument
                                ->showUserDocuments($request);

            $user = Reseller::where('id', $request->userid)->first();

            $totalusers = $this->client->resellerTotalUsers(['reseller_id' => $request->userid]);

            $totdaysproductsale = $this->productsale->resellerTotalSalesInToday(['invoice_owner_id' => $request->userid]);

            $totalproductsalebyroot = $this->productsale->resellerTotalSalesByRoot(['invoice_owner_id' => $request->userid]);

            $totalrevinue = $this->productsale->resellerTotalRevinueInTodayByRoot(['invoice_owner_id' => $request->userid]);
            
            $totalrevinueinmonth = $this->productsale->resellerTotalRevinueInCurrentMonthByRoot(['invoice_owner_id' => $request->userid]);
            
            $totalrevinueinyear = $this->productsale->resellerTotalRevinueInCurrentYearByRoot(['invoice_owner_id' => $request->userid]);

            $todayssmssent = $this->smssent->resellerClientTodaysSmsSentHistoryForRoot(['owner_id' => $request->userid]);
            
            $thisweekssmssent = $this->smssent->resellerThisWeekSmsSentHistoryForRoot(['owner_id' => $request->userid]);
            
            $thismonthsmssent = $this->smssent->resellerThisMonthSmsSentHistoryForRoot(['owner_id' => $request->userid]);

            $todaysenrollclient = $this->client->todaysResellerEnrollClientForRoot(['reseller_id' => $request->userid]);
            
            $monthlyenrollclient = $this->client->resellerMonthlyEnrollClientForRoot(['reseller_id' => $request->userid]);


            $totdaymasksentbal = $this->smssent->resellerClientConsumeMaskSmsBalance($request->userid);
            $todaynonmasksentbal = $this->smssent->resellerClientConsumeNonMaskSmsBalance($request->userid);
            $todayvoicesentbal = $this->smssent->resellerClientConsumeVoiceSmsBalance($request->userid);

            $totalsentsms = ($totdaymasksentbal+$todaynonmasksentbal+$todayvoicesentbal);
            
            //$thisweeksentsmsbal = $this->smssent->resellerClientThisWeekConsumeMaskSmsBalance(Auth::guard('reseller')->user()->id);
            //$thismonthsentsmsbal = $this->smssent->resellerClientThisMonthConsumeMaskSmsBalance(Auth::guard('reseller')->user()->id);


            $clientmasksmsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'mask');
            $clientnonmasksmsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'nomask');
            $clientvoicemsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'voice');

            $totalmaskbal = ($clientmasksmsbal-$this->smssent->resellerTotalConsumeMaskBalance($request->userid));
            $totalnonmaskbal = ($clientnonmasksmsbal-$this->smssent->resellerTotalConsumeNonMaskBalance($request->userid));
            $totalvoicebal = ($clientvoicemsbal-$this->smssent->resellerTotalConsumeVoiceBalance($request->userid));
            
            return view('smsview.reseller.reseller-profile-document',compact(
                'user',
                'totalusers',
                'totdaysproductsale',
                'totalproductsalebyroot',
                'totalrevinue',
                'totalrevinueinmonth',
                'totalrevinueinyear',
                'todayssmssent',
                'thisweekssmssent',
                'thismonthsmssent',
                'todaysenrollclient',
                'monthlyenrollclient',
                //'totalsentsms',
                //'thisweeksentsmsbal',
                //'thismonthsentsmsbal',
                'totalmaskbal',
                'totalnonmaskbal',
                'totalvoicebal',
                'request',
                'clientDocuments',
                'countries'
            ));
        } else {
            return view('smsview.pagenotfound.404');
        }   
    }


    /**
     * Reseller client profile
     *
     * @param Request $request
     * @return void
     */
    public function resellerProfileSenderid(Request $request) {

        if (Reseller::where('id',$request->userid)->exists()) {

            $countries = $this->country
                        ->show();

            $clientDocuments = $this->resellerDocument
                                ->showUserDocuments($request);

            $user = Reseller::where('id', $request->userid)->first();

            $totalusers = $this->client->resellerTotalUsers(['reseller_id' => $request->userid]);

            $totdaysproductsale = $this->productsale->resellerTotalSalesInToday(['invoice_owner_id' => $request->userid]);

            $totalproductsalebyroot = $this->productsale->resellerTotalSalesByRoot(['invoice_owner_id' => $request->userid]);

            $totalrevinue = $this->productsale->resellerTotalRevinueInTodayByRoot(['invoice_owner_id' => $request->userid]);
            
            $totalrevinueinmonth = $this->productsale->resellerTotalRevinueInCurrentMonthByRoot(['invoice_owner_id' => $request->userid]);
            
            $totalrevinueinyear = $this->productsale->resellerTotalRevinueInCurrentYearByRoot(['invoice_owner_id' => $request->userid]);

            $todayssmssent = $this->smssent->resellerClientTodaysSmsSentHistoryForRoot(['owner_id' => $request->userid]);
            
            $thisweekssmssent = $this->smssent->resellerThisWeekSmsSentHistoryForRoot(['owner_id' => $request->userid]);
            
            $thismonthsmssent = $this->smssent->resellerThisMonthSmsSentHistoryForRoot(['owner_id' => $request->userid]);

            $todaysenrollclient = $this->client->todaysResellerEnrollClientForRoot(['reseller_id' => $request->userid]);
            
            $monthlyenrollclient = $this->client->resellerMonthlyEnrollClientForRoot(['reseller_id' => $request->userid]);


            $totdaymasksentbal = $this->smssent->resellerClientConsumeMaskSmsBalance($request->userid);
            $todaynonmasksentbal = $this->smssent->resellerClientConsumeNonMaskSmsBalance($request->userid);
            $todayvoicesentbal = $this->smssent->resellerClientConsumeVoiceSmsBalance($request->userid);

            $totalsentsms = ($totdaymasksentbal+$todaynonmasksentbal+$todayvoicesentbal);
            
            //$thisweeksentsmsbal = $this->smssent->resellerClientThisWeekConsumeMaskSmsBalance(Auth::guard('reseller')->user()->id);
            //$thismonthsentsmsbal = $this->smssent->resellerClientThisMonthConsumeMaskSmsBalance(Auth::guard('reseller')->user()->id);


            $clientmasksmsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'mask');
            $clientnonmasksmsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'nomask');
            $clientvoicemsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'voice');

            $totalmaskbal = ($clientmasksmsbal-$this->smssent->resellerTotalConsumeMaskBalance($request->userid));
            $totalnonmaskbal = ($clientnonmasksmsbal-$this->smssent->resellerTotalConsumeNonMaskBalance($request->userid));
            $totalvoicebal = ($clientvoicemsbal-$this->smssent->resellerTotalConsumeVoiceBalance($request->userid));

            $userid = $request->userid;

            $senderids = SmsSenderIdResource::collection(
                SmsSender::whereNotIn('id', function($query) use($userid){
                    $query->select('sms_sender_id')
                          ->from('reseller_senders')
                          ->where('reseller_id',$userid);
                })
                ->where('operator_id','!=',NULL)
                ->get()
            );
    

            return view('smsview.reseller.reseller-profile-senderid',compact(
                'senderids',
                'user',
                'totalusers',
                'totdaysproductsale',
                'totalproductsalebyroot',
                'totalrevinue',
                'totalrevinueinmonth',
                'totalrevinueinyear',
                'todayssmssent',
                'thisweekssmssent',
                'thismonthsmssent',
                'todaysenrollclient',
                'monthlyenrollclient',
                //'totalsentsms',
                //'thisweeksentsmsbal',
                //'thismonthsentsmsbal',
                'totalmaskbal',
                'totalnonmaskbal',
                'totalvoicebal',
                'request',
                'clientDocuments',
                'countries'
            ));
        } else {
            return view('smsview.pagenotfound.404');
        }   
    }


    /**
     * Reseller client profile
     *
     * @param Request $request
     * @return void
     */
    public function resellerProfileManageInvoice(Request $request) {

        if (Reseller::where('id',$request->userid)->exists()) {

            $countries = $this->country
                        ->show();

            $clientDocuments = $this->resellerDocument
                                ->showUserDocuments($request);

            $user = Reseller::where('id', $request->userid)->first();

            $totalusers = $this->client->resellerTotalUsers(['reseller_id' => $request->userid]);

            $totdaysproductsale = $this->productsale->resellerTotalSalesInToday(['invoice_owner_id' => $request->userid]);

            $totalproductsalebyroot = $this->productsale->resellerTotalSalesByRoot(['invoice_owner_id' => $request->userid]);

            $totalrevinue = $this->productsale->resellerTotalRevinueInTodayByRoot(['invoice_owner_id' => $request->userid]);
            
            $totalrevinueinmonth = $this->productsale->resellerTotalRevinueInCurrentMonthByRoot(['invoice_owner_id' => $request->userid]);
            
            $totalrevinueinyear = $this->productsale->resellerTotalRevinueInCurrentYearByRoot(['invoice_owner_id' => $request->userid]);

            $todayssmssent = $this->smssent->resellerClientTodaysSmsSentHistoryForRoot(['owner_id' => $request->userid]);
            
            $thisweekssmssent = $this->smssent->resellerThisWeekSmsSentHistoryForRoot(['owner_id' => $request->userid]);
            
            $thismonthsmssent = $this->smssent->resellerThisMonthSmsSentHistoryForRoot(['owner_id' => $request->userid]);

            $todaysenrollclient = $this->client->todaysResellerEnrollClientForRoot(['reseller_id' => $request->userid]);
            
            $monthlyenrollclient = $this->client->resellerMonthlyEnrollClientForRoot(['reseller_id' => $request->userid]);


            $totdaymasksentbal = $this->smssent->resellerClientConsumeMaskSmsBalance($request->userid);
            $todaynonmasksentbal = $this->smssent->resellerClientConsumeNonMaskSmsBalance($request->userid);
            $todayvoicesentbal = $this->smssent->resellerClientConsumeVoiceSmsBalance($request->userid);

            $totalsentsms = ($totdaymasksentbal+$todaynonmasksentbal+$todayvoicesentbal);
            
            //$thisweeksentsmsbal = $this->smssent->resellerClientThisWeekConsumeMaskSmsBalance(Auth::guard('reseller')->user()->id);
            //$thismonthsentsmsbal = $this->smssent->resellerClientThisMonthConsumeMaskSmsBalance(Auth::guard('reseller')->user()->id);


            $clientmasksmsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'mask');
            $clientnonmasksmsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'nomask');
            $clientvoicemsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'voice');

            $totalmaskbal = ($clientmasksmsbal-$this->smssent->resellerTotalConsumeMaskBalance($request->userid));
            $totalnonmaskbal = ($clientnonmasksmsbal-$this->smssent->resellerTotalConsumeNonMaskBalance($request->userid));
            $totalvoicebal = ($clientvoicemsbal-$this->smssent->resellerTotalConsumeVoiceBalance($request->userid));

            $userid = $request->userid;

            $senderids = SmsSenderIdResource::collection(
                SmsSender::whereNotIn('id', function($query) use($userid){
                    $query->select('sms_sender_id')
                          ->from('reseller_senders')
                          ->where('reseller_id',$userid);
                })
                ->where('operator_id','!=',NULL)
                ->get()
            );

            $groups = $this->accountshead->getAllGroupAccountsHeadById(5);
    

            return view('smsview.reseller.reseller-profile-manage-invoice',compact(
                'groups',
                'senderids',
                'user',
                'totalusers',
                'totdaysproductsale',
                'totalproductsalebyroot',
                'totalrevinue',
                'totalrevinueinmonth',
                'totalrevinueinyear',
                'todayssmssent',
                'thisweekssmssent',
                'thismonthsmssent',
                'todaysenrollclient',
                'monthlyenrollclient',
                //'totalsentsms',
                //'thisweeksentsmsbal',
                //'thismonthsentsmsbal',
                'totalmaskbal',
                'totalnonmaskbal',
                'totalvoicebal',
                'request',
                'clientDocuments',
                'countries'
            ));
        } else {
            return view('smsview.pagenotfound.404');
        }   
    }


    /**
     * Reseller client profile
     *
     * @param Request $request
     * @return void
     */
    public function resellerProfileSmsSale(Request $request) {

        if (Reseller::where('id',$request->userid)->exists()) {

            $countries = $this->country
                        ->show();

            $clientDocuments = $this->resellerDocument
                                ->showUserDocuments($request);

            $user = Reseller::where('id', $request->userid)->first();

            $totalusers = $this->client->resellerTotalUsers(['reseller_id' => $request->userid]);

            $totdaysproductsale = $this->productsale->resellerTotalSalesInToday(['invoice_owner_id' => $request->userid]);

            $totalproductsalebyroot = $this->productsale->resellerTotalSalesByRoot(['invoice_owner_id' => $request->userid]);

            $totalrevinue = $this->productsale->resellerTotalRevinueInTodayByRoot(['invoice_owner_id' => $request->userid]);
            
            $totalrevinueinmonth = $this->productsale->resellerTotalRevinueInCurrentMonthByRoot(['invoice_owner_id' => $request->userid]);
            
            $totalrevinueinyear = $this->productsale->resellerTotalRevinueInCurrentYearByRoot(['invoice_owner_id' => $request->userid]);

            $todayssmssent = $this->smssent->resellerClientTodaysSmsSentHistoryForRoot(['owner_id' => $request->userid]);
            
            $thisweekssmssent = $this->smssent->resellerThisWeekSmsSentHistoryForRoot(['owner_id' => $request->userid]);
            
            $thismonthsmssent = $this->smssent->resellerThisMonthSmsSentHistoryForRoot(['owner_id' => $request->userid]);

            $todaysenrollclient = $this->client->todaysResellerEnrollClientForRoot(['reseller_id' => $request->userid]);
            
            $monthlyenrollclient = $this->client->resellerMonthlyEnrollClientForRoot(['reseller_id' => $request->userid]);


            $totdaymasksentbal = $this->smssent->resellerClientConsumeMaskSmsBalance($request->userid);
            $todaynonmasksentbal = $this->smssent->resellerClientConsumeNonMaskSmsBalance($request->userid);
            $todayvoicesentbal = $this->smssent->resellerClientConsumeVoiceSmsBalance($request->userid);

            $totalsentsms = ($totdaymasksentbal+$todaynonmasksentbal+$todayvoicesentbal);
            
            //$thisweeksentsmsbal = $this->smssent->resellerClientThisWeekConsumeMaskSmsBalance(Auth::guard('reseller')->user()->id);
            //$thismonthsentsmsbal = $this->smssent->resellerClientThisMonthConsumeMaskSmsBalance(Auth::guard('reseller')->user()->id);


            $clientmasksmsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'mask');
            $clientnonmasksmsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'nomask');
            $clientvoicemsbal = $this->productsale->getResellerSmsBalanceByCategory($request->userid,'voice');

            $totalmaskbal = ($clientmasksmsbal-$this->smssent->resellerTotalConsumeMaskBalance($request->userid));
            $totalnonmaskbal = ($clientnonmasksmsbal-$this->smssent->resellerTotalConsumeNonMaskBalance($request->userid));
            $totalvoicebal = ($clientvoicemsbal-$this->smssent->resellerTotalConsumeVoiceBalance($request->userid));

            $userid = $request->userid;

            $senderids = SmsSenderIdResource::collection(
                SmsSender::whereNotIn('id', function($query) use($userid){
                    $query->select('sms_sender_id')
                          ->from('reseller_senders')
                          ->where('reseller_id',$userid);
                })
                ->where('operator_id','!=',NULL)
                ->get()
            );

            $groups = $this->accountshead->getAllGroupAccountsHeadById(5);
    

            return view('smsview.reseller.reseller-profile-smssale',compact(
                'groups',
                'senderids',
                'user',
                'totalusers',
                'totdaysproductsale',
                'totalproductsalebyroot',
                'totalrevinue',
                'totalrevinueinmonth',
                'totalrevinueinyear',
                'todayssmssent',
                'thisweekssmssent',
                'thismonthsmssent',
                'todaysenrollclient',
                'monthlyenrollclient',
                //'totalsentsms',
                //'thisweeksentsmsbal',
                //'thismonthsentsmsbal',
                'totalmaskbal',
                'totalnonmaskbal',
                'totalvoicebal',
                'request',
                'clientDocuments',
                'countries'
            ));
        } else {
            return view('smsview.pagenotfound.404');
        }   
    }

    public function clientTotalMonthlySms(Request $request) {
        $montlysmses = DB::select(DB::raw("
                    select user_id,sum(number_of_sms) totalsms,monthname(submitted_at) month,year(submitted_at) year
                    from archive_sent_smses
                    where user_id in(select id from users where reseller_id=$request->userid)
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
    public function resellerClientProfileIndex(Request $request)
    {   
        $this->userbalance->calculateUserBalance($request->userid);
        $countries = $this->country
                        ->show();

        $clientDocuments = $this->clientDocument
                                ->showUserDocuments($request);

        $user = User::where('id', $request->userid)->first();

        $totdaymasksentbal = $this->smssent->clientConsumeMaskSmsBalance($request->userid);
        $todaynonmasksentbal = $this->smssent->clientConsumeNonMaskSmsBalance($request->userid);
        $todayvoicesentbal = $this->smssent->clientConsumeVoiceSmsBalance($request->userid);

        $totalsentsms = ($totdaymasksentbal+$todaynonmasksentbal+$todayvoicesentbal);

        $thisweeksentsmsbal = $this->smssent->clientThisWeekConsumeMaskSmsBalance($request->userid);
        $thismonthsentsmsbal = $this->smssent->clientThisMonthConsumeMaskSmsBalance($request->userid);

        $clientmasksmsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'mask');
        $clientnonmasksmsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'nomask');
        $clientvoicemsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'voice');

        $totalmaskbal = ($clientmasksmsbal-$this->smssent->totalConsumeMaskBalance($request->userid));
        $totalnonmaskbal = ($clientnonmasksmsbal-$this->smssent->totalConsumeNonMaskBalance($request->userid));
        $totalvoicebal = ($clientvoicemsbal-$this->smssent->totalConsumeVoiceBalance($request->userid));

        
        
        return view('smsview.rootadmin.client-profile-index',compact('totalsentsms','thisweeksentsmsbal','thismonthsentsmsbal','user','countries','request','clientDocuments','totalmaskbal','totalnonmaskbal','totalvoicebal'));
    }

    /**
     * Root Client edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function resellerClientProfile(Request $request)
    {   
        $this->userbalance->calculateUserBalance($request->userid);
        $countries = $this->country
                        ->show();

        
        if (User::where('id', $request->userid)->exists()) {

            $user = User::where('id', $request->userid)->first();

            $clientmasksmsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'mask');
            $clientnonmasksmsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'nomask');
            $clientvoicemsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'voice');

            $totalmaskbal = ($clientmasksmsbal-$this->smssent->totalConsumeMaskBalance($request->userid));
            $totalnonmaskbal = ($clientnonmasksmsbal-$this->smssent->totalConsumeNonMaskBalance($request->userid));
            $totalvoicebal = ($clientvoicemsbal-$this->smssent->totalConsumeVoiceBalance($request->userid));

            
            
            return view('smsview.rootadmin.client-profile',compact('user','countries','request','totalmaskbal','totalnonmaskbal','totalvoicebal'));
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
    public function resellerClientProfileDocument(Request $request)
    {   
        $this->userbalance->calculateUserBalance($request->userid);
        $countries = $this->country
                        ->show();

        $clientDocuments = $this->clientDocument
                                ->showUserDocuments($request);

        $user = User::where('id', $request->userid)->first();

        $clientmasksmsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'mask');
        $clientnonmasksmsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'nomask');
        $clientvoicemsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'voice');

        $totalmaskbal = ($clientmasksmsbal-$this->smssent->totalConsumeMaskBalance($request->userid));
        $totalnonmaskbal = ($clientnonmasksmsbal-$this->smssent->totalConsumeNonMaskBalance($request->userid));
        $totalvoicebal = ($clientvoicemsbal-$this->smssent->totalConsumeVoiceBalance($request->userid));
        
        return view('smsview.rootadmin.client-profile-document',compact('user','countries','request','clientDocuments','totalmaskbal','totalnonmaskbal','totalvoicebal'));
    }

    

    public function resellerClientTotalMonthlySms(Request $request) {
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

    public function searchUnAssignedSenderid(Request $request) {
        $userid = $request->userid;
        $resellerid = Auth::guard('reseller')->user()->id;
        $senderids = SmsSenderIdResource::collection(
            SmsSender::whereNotIn('id', function($query) use($userid){
                $query->select('sms_sender_id')
                    ->from('user_senders')
                    ->where('user_id',$userid);
            })
            ->where('operator_id','!=',NULL)
            ->whereNotIn('id', function($query) use($resellerid){
                $query->select('sms_sender_id')
                      ->from('reseller_senders')
                      ->where('reseller_id', $resellerid);
            })
            ->where('sender_name','LIKE', $request->search.'%')
            ->get()
        );

        return $senderids;


    }

    /**
     * Root Client edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function resellerClientProfileTemplate(Request $request)
    {   
        $this->userbalance->calculateUserBalance($request->userid);
        $countries = $this->country
                        ->show();

        $clientDocuments = $this->clientDocument
                                ->showUserDocuments($request);

        $user = User::where('id', $request->userid)->first();

        $clientmasksmsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'mask');
        $clientnonmasksmsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'nomask');
        $clientvoicemsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'voice');

        $totalmaskbal = ($clientmasksmsbal-$this->smssent->totalConsumeMaskBalance($request->userid));
        $totalnonmaskbal = ($clientnonmasksmsbal-$this->smssent->totalConsumeNonMaskBalance($request->userid));
        $totalvoicebal = ($clientvoicemsbal-$this->smssent->totalConsumeVoiceBalance($request->userid));
        
        return view('smsview.rootadmin.client-profile-template',compact('user','countries','request','clientDocuments','totalmaskbal','totalnonmaskbal','totalvoicebal'));
    }


    /**
     * Root Client edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function resellerClientProfileManageInvoice(Request $request)
    {   
        $countries = $this->country
                        ->show();

        $this->userbalance->calculateUserBalance($request->userid);

        $clientDocuments = $this->clientDocument
                                ->showUserDocuments($request);

        $user = User::where('id', $request->userid)->first();

        $clientmasksmsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'mask');
        $clientnonmasksmsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'nomask');
        $clientvoicemsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'voice');

        $totalmaskbal = ($clientmasksmsbal-$this->smssent->totalConsumeMaskBalance($request->userid));
        $totalnonmaskbal = ($clientnonmasksmsbal-$this->smssent->totalConsumeNonMaskBalance($request->userid));
        $totalvoicebal = ($clientvoicemsbal-$this->smssent->totalConsumeVoiceBalance($request->userid));

        $groups = $this->accountshead->getAllGroupAccountsHeadById(5);
        
        return view('smsview.rootadmin.client-profile-manage-invoice',compact('user','countries','request','clientDocuments','totalmaskbal','totalnonmaskbal','totalvoicebal','groups'));
    }


    /**
     * Root Client edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function resellerClientProfileSmsSale(Request $request)
    {   
        $countries = $this->country
                        ->show();

        $this->userbalance->calculateUserBalance($request->userid);

        $clientDocuments = $this->clientDocument
                                ->showUserDocuments($request);

        $user = User::where('id', $request->userid)->first();

        $clientmasksmsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'mask');
        $clientnonmasksmsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'nomask');
        $clientvoicemsbal = $this->productsale->getSmsBalanceByCategory($request->userid,'voice');

        $totalmaskbal = ($clientmasksmsbal-$this->smssent->totalConsumeMaskBalance($request->userid));
        $totalnonmaskbal = ($clientnonmasksmsbal-$this->smssent->totalConsumeNonMaskBalance($request->userid));
        $totalvoicebal = ($clientvoicemsbal-$this->smssent->totalConsumeVoiceBalance($request->userid));

        $groups = $this->accountshead->getAllGroupAccountsHeadById(5);
        
        return view('smsview.rootadmin.client-profile-smssale',compact('user','countries','request','clientDocuments','totalmaskbal','totalnonmaskbal','totalvoicebal','groups'));
    }

    /**
     * User registration form
     *
     * @return void
     */
    public function smsappUserRegister()
    {
        $countries = $this->country->show();
        return view('smsview.common.client-registration', compact('countries'));
    }

    /**
     * Reseller user list view
     *
     * @return void
     */
    public function resellerClientList() {

        return view('smsview.reseller.reseller-clients');

    }

    /**
     * Client user list data
     *
     * @return void
     */
    public function clientData() {

        return $this->reseller->showClients();

    }

    public function clientLoginFromRoot(Request $request)
    {
        
        if(Auth::guard('reseller')->check() && User::where('email',$request->email)->where('created_by','reseller')->where('reseller_id',Auth::guard('reseller')->user()->id)->exists())
        {
            $user = User::where('email',$request->email)->where('created_by','reseller')->where('reseller_id',Auth::guard('reseller')->user()->id)->first();
            Auth::guard('reseller')->logout();
            Auth::guard('web')->login($user);
            return redirect()->route('client');
        }

        return response()->json(['errmsg' => 'User Not Found'],406);
    }

    public function resellerBalance(Request $request)
    {
        $resellerid = !empty($request->userid) ? $request->userid : Auth::guard('reseller')->user()->id;
        $totdaymasksentbal = $this->smssent->resellerClientConsumeMaskSmsBalance($resellerid);
        $todaynonmasksentbal = $this->smssent->resellerClientConsumeNonMaskSmsBalance($resellerid);
        $todayvoicesentbal = $this->smssent->resellerClientConsumeVoiceSmsBalance($resellerid);

        $totalsentsms = ($totdaymasksentbal+$todaynonmasksentbal+$todayvoicesentbal);

        $thisweeksentsmsbal = $this->smssent->resellerClientThisWeekConsumeMaskSmsBalance($resellerid);
        $thismonthsentsmsbal = $this->smssent->resellerClientThisMonthConsumeMaskSmsBalance($resellerid);


        $clientmasksmsbal = $this->productsale->getResellerSmsBalanceByCategory($resellerid,'mask');
        $clientnonmasksmsbal = $this->productsale->getResellerSmsBalanceByCategory($resellerid,'nomask');
        $clientvoicemsbal = $this->productsale->getResellerSmsBalanceByCategory($resellerid,'voice');

        $totalmaskbal = ($clientmasksmsbal-$this->smssent->resellerTotalConsumeMaskBalance($resellerid));
        $totalnonmaskbal = ($clientnonmasksmsbal-$this->smssent->resellerTotalConsumeNonMaskBalance($resellerid));
        $totalvoicebal = ($clientvoicemsbal-$this->smssent->resellerTotalConsumeVoiceBalance($resellerid));

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

}
