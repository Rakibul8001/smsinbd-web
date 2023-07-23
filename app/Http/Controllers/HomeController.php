<?php

namespace App\Http\Controllers;

use App\Core\ClientDocuments\ClientDocumentsInterface;
use App\Core\ResellerDocuments\ResellerDocumentsInterface;
use App\Core\AccountsChart\AccountsHead;
use App\RootUser;
use Illuminate\Http\Request;
use App\Core\Users\RootUserInterface;
use App\Core\Countries\CountriesInterface;
use App\Core\ProductSales\ProductSales;
use App\Core\UserCountSms\UserCountSms;
use App\Core\Users\ClientInterface;
use App\Core\Users\ManagerInterface;
use App\Core\Users\ResellerInterface;
use App\Datatables\DataTableClass;
use App\Core\BalanceReconciliation\BalanceReconciliation;
use App\Http\Resources\SmsSenderIdResource;
use App\Manager;
use App\Reseller;
use App\SmsSender;
use App\User;
use Auth;
use DB;
use Carbon\Carbon;

//mod
use App\SmsSingle;
use App\SmsCampaigns;


class HomeController extends Controller
{
    
    /**
     * Root user service
     *
     * @var App\Core\Users\RootUserRepository
     */
    protected $root;

    /**
     * Country service
     *
     * @var Object App\Core\Countries\Countries
     */
    protected $country;


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
      * Client Document service
      *
      * @var App\Core\ClientDocuments\resellerDocumentUpload
      */
     protected $resellerDocument;

     /**
      * Client Service
      *
      * @var App\Core\Users\ClientRepository
      */
     protected $client;

     /**
      * Manager service
      *
      * @var App\Core\Users\ManagerRepository
      */
     protected $manager;

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

     public $userbalance;

     public $accountshead;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        RootUserInterface $root,
        ManagerInterface $manager,
        ResellerInterface $reseller,
        CountriesInterface $country,
        ClientInterface $client,
        ProductSales $productsale,
        ClientDocumentsInterface $clientDocument,
        ResellerDocumentsInterface $resellerDocument,
        UserCountSms $smssent,
        BalanceReconciliation $userbalance,
        AccountsHead $accountshead
    )
    {
        $this->middleware('auth:root,manager');

        $this->root = $root;

        $this->country = $country;

        $this->client = $client;

        $this->clientDocument = $clientDocument;
        
        $this->resellerDocument = $resellerDocument;

        $this->manager = $manager;

        $this->reseller = $reseller;

        $this->productsale = $productsale;

        $this->smssent = $smssent;

        $this->userbalance = $userbalance;

        $this->accountshead = $accountshead;

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $totalmanager = $this->manager->totalSupportManagers();

        $totalreseller = $this->reseller->totalResellers();

        $totalusers = $this->client->totalUsers();

        $totdaysproductsale = $this->productsale->totalSalesInToday();

        $totalproductsalebyroot = $this->productsale->totalSalesByRoot();

        $totalrevinue = $this->productsale->totalRevinueInTodayByRoot();
        
        $totalrevinueinmonth = $this->productsale->totalRevinueInCurrentMonthByRoot();
        
        $totalrevinueinyear = $this->productsale->totalRevinueInCurrentYearByRoot();

        $todayssmssent = $this->smssent->todaysSmsSentHistoryForRoot();
        
        //$thisweekssmssent = $this->smssent->thisWeekSmsSentHistoryForRoot();
        
        //$thismonthsmssent = $this->smssent->thisMonthSmsSentHistoryForRoot();

        $todaysenrollclient = $this->client->todaysEnrollClientForRoot();
        
        $monthlyenrollclient = $this->client->monthlyEnrollClientForRoot();
        
        
        // edit by rubel
        $todaysCampaignSMS = DB::table('sms_campaigns')->select(DB::raw('sum(sms_campaigns.total_numbers * sms_campaigns.sms_qty) AS total_sms'))->whereDate('created_at', '=', date('Y-m-d'))->first();
        $todaysCampaignSMS = $todaysCampaignSMS->total_sms;
        
        $todaysSingleSMS = SmsSingle::whereDate('created_at', '=', date('Y-m-d'))->sum('qty');
        
        $todaysTotalCampaign = SmsCampaigns::whereDate('created_at', '=', date('Y-m-d'))->count();
        
        $last7date = Carbon::now()->subDays(7);
        
        $last7daysCampaignSMS = DB::table('sms_campaigns')->select(DB::raw('sum(sms_campaigns.total_numbers * sms_campaigns.sms_qty) AS total_sms'))->where('created_at', '>=', $last7date)->first();
        $last7daysCampaignSMS = $last7daysCampaignSMS->total_sms;
        
        $last7daysSingleSMS = SmsSingle::where('created_at', '>=', $last7date)->sum('qty');
        
        $last7daysTotalCampaign = SmsCampaigns::where('created_at', '>=', $last7date)->count();
        
        
        $last30date = Carbon::now()->subDays(30);
        
        $last30daysCampaignSMS = DB::table('sms_campaigns')->select(DB::raw('sum(sms_campaigns.total_numbers * sms_campaigns.sms_qty) AS total_sms'))->where('created_at', '>=', $last30date)->first();
        $last30daysCampaignSMS = $last30daysCampaignSMS->total_sms;
        
        $last30daysSingleSMS = SmsSingle::where('created_at', '>=', $last30date)->sum('qty');
        
        $last30daysTotalCampaign = SmsCampaigns::where('created_at', '>=', $last30date)->count();
        
        //edit end

        return view('smsview.rootadmin.index', compact(
            'totalmanager',
            'totalreseller',
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
            'todaysCampaignSMS',
            'todaysSingleSMS',
            'todaysTotalCampaign',
            'last7daysCampaignSMS',
            'last7daysSingleSMS',
            'last7daysTotalCampaign',
            'last30daysCampaignSMS',
            'last30daysSingleSMS',
            'last30daysTotalCampaign'
        ));
    }


    /**
     * User registration form
     *
     * @return void
     */
    public function smsappUserRegister()
    {
        $countries = $this->country
                        ->show();

        return view('smsview.common.client-registration',compact('countries'));
    }


    /**
     * Root User registration form as per requirement
     *
     * @return void
     */
    public function smsappRootUserRegister()
    {
        $countries = $this->country
                        ->show();

        return view('smsview.common.root-user-registration',compact('countries'));
    }

    /**
     * Support Manager registration form as per requirement
     *
     * @return void
     */
    public function smsappManagerUserRegister()
    {
        $countries = $this->country
                        ->show();

        return view('smsview.common.support-manager-registration',compact('countries'));
    }

    /**
     * Support Manager registration form as per requirement
     *
     * @return void
     */
    public function smsappResellerRegister()
    {
        $countries = $this->country
                        ->show();

        return view('smsview.common.reseller-registration',compact('countries'));
    }


    /**
     * Root user list view
     *
     * @return void
     */
    public function rootUserList() {

        return view('smsview.rootadmin.root-users');

    }

    /**
     * Root user list data
     *
     * @return void
     */
    public function rootUserData() {

        return $this->root
                    ->showRootUsers();

    }


    /**
     * Manager user list view
     *
     * @return void
     */
    public function rootManagerList() {

        return view('smsview.rootadmin.root-managers');

    }

    /**
     * Manager user list data
     *
     * @return void
     */
    public function supportManagerData() {

        return $this->root
                    ->showManagers();

    }

    /**
     * Reseller user list view
     *
     * @return void
     */
    public function rootResellerList() {

        return view('smsview.rootadmin.root-resellers');

    }

    /**
     * Reseller user list data
     *
     * @return void
     */
    public function resellerData() {

        return $this->root
                    ->showResellers();

    }

    /**
     * Reseller user list view
     *
     * @return void
     */
    public function rootClientList() {

        return view('smsview.rootadmin.root-clients');

    }

    /**
     * Client user list data
     *
     * @return void
     */
    public function clientData() {

        return $this->root
                    ->showClients();

    }
    
    public function clientResellerData(Request $request){
        $reseller = Reseller::find($request->reseller_id);
        $res = [];
        $res['name']= $reseller->name;
        return $res;
    }

    /**
     * Root user edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function rootUserEdit(Request $request, RootUser $user)
    { 
        $countries = $this->country
                        ->show();
        
        return view('smsview.rootadmin.root-user-edit',compact('user','countries'));
    }

    /**
     * Root Manager edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function rootManagerEdit(Request $request, Manager $user)
    { 
        $countries = $this->country
                        ->show();
        
        return view('smsview.rootadmin.root-manager-edit',compact('user','countries'));
    }


    /**
     * Root Reseller edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function rootResellerEdit(Request $request)
    { 
        $countries = $this->country
                        ->show();

        $clientDocuments = $this->resellerDocument
                                ->showUserDocuments($request);

        $user = Reseller::where('id', $request->userid)->first();
        
        return view('smsview.rootadmin.root-reseller-edit',compact('user','countries','request','clientDocuments'));

    }

    /**
     * Root Client edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function rootClientEdit(Request $request)
    {   
        $countries = $this->country
                        ->show();

        $clientDocuments = $this->clientDocument
                                ->showUserDocuments($request);

        $user = User::where('id', $request->userid)->first();
        
        return view('smsview.rootadmin.root-client-edit',compact('user','countries','request','clientDocuments'));
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
    public function rootClientProfileDocument(Request $request)
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


    /**
     * Root Client edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function rootClientProfileIndex(Request $request)
    {   
        // $this->userbalance->calculateUserBalance($request->userid);
        $countries = $this->country
                        ->show();

        $clientDocuments = $this->clientDocument
                                ->showUserDocuments($request);

        $user = User::where('id', $request->userid)->first();

        $userId = $user->id;
        $today = date("Y-m-d");


        $individualMaskToday = SmsSingle::where('user_id', $user->id)->where('category', 1)->whereDate('created_at', date("Y-m-d"))->sum('qty');

        $campaignMaskToday = DB::table('sms_campaigns')
                ->selectRaw('SUM(sms_qty * total_numbers) as todaystotalsms')
                ->where('user_id', $user->id)->where('category', 1)->whereDate('created_at', date("Y-m-d"))
                ->first()->todaystotalsms;


        $individualNonMaskToday = SmsSingle::where('user_id', $user->id)->where('category', 2)->whereDate('created_at', date("Y-m-d"))->sum('qty');

        $campaignNonMaskToday = DB::table('sms_campaigns')
                ->selectRaw('SUM(sms_qty * total_numbers) as todaystotalsms')
                ->where('user_id', $user->id)->where('category', 2)->whereDate('created_at', date("Y-m-d"))
                ->first()->todaystotalsms;

        $individualVoiceToday = SmsSingle::where('user_id', $user->id)->where('category', 2)->whereDate('created_at', date("Y-m-d"))->sum('qty');

        $campaignVoiceToday = DB::table('sms_campaigns')
                ->selectRaw('SUM(sms_qty * total_numbers) as todaystotalsms')
                ->where('user_id', $user->id)->where('category', 2)->whereDate('created_at', date("Y-m-d"))
                ->first()->todaystotalsms;


        $totdaymasksentbal = $individualMaskToday + $campaignMaskToday;
        $todaynonmasksentbal = $individualNonMaskToday + $campaignNonMaskToday;
        $todayvoicesentbal = $individualVoiceToday + $campaignVoiceToday;

        $totalsentsms = ($totdaymasksentbal+$todaynonmasksentbal+$todayvoicesentbal);

        $thisweeksentsmsbal = 'Unavailable';
        $thismonthsentsmsbal = 'Unavailable';



        $totalmaskbal = $user->mask_balance;
        $totalnonmaskbal = $user->nonmask_balance;
        $totalvoicebal = $user->voice_balance;

        
        
        return view('smsview.rootadmin.client-profile-index',compact('totalsentsms','thisweeksentsmsbal','thismonthsentsmsbal','user','countries','request','clientDocuments','totalmaskbal','totalnonmaskbal','totalvoicebal'));
    }


    public function clientTotalMonthlySms(Request $request) {
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

        
        return view('smsview.rootadmin.client-profile-senderid',compact('user','countries','request','clientDocuments','totalmaskbal','totalnonmaskbal','totalvoicebal','senderids'));
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
    public function rootClientProfileSmsSale(Request $request)
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
     * Root Client edit
     *
     * @param Request $request
     * @param RootUser $user
     * @return void
     */
    public function rootClientProfileManageInvoice(Request $request)
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

    public function searchUnAssignedSenderid(Request $request) {

        $userid = $request->userid;
        $senderids = SmsSenderIdResource::collection(
            SmsSender::whereNotIn('id', function($query) use($userid){
                $query->select('sms_sender_id')
                    ->from('user_senders')
                    ->where('user_id',$userid);
            })
            ->where('operator_id','!=',NULL)
            ->where('sender_name','LIKE', $request->search.'%')
            ->get()
        );

        return $senderids;


    }

    public function activeClients()
    {
        return $this->client
                    ->activeClients();
    }

    public function clientLoginFromRoot(Request $request)
    {
        if((Auth::guard('root')->check() || Auth::guard('manager')->check()) && User::where('email',$request->email)->exists())
        {
            $user = User::where('email',$request->email)->first();
            if (Auth::guard('root')->check()) {
                Auth::guard('root')->logout();
            }

            if (Auth::guard('manager')->check()) {
                Auth::guard('manager')->logout();
            }

            Auth::guard('web')->login($user);
            return redirect()->route('client');
        }

        return response()->json(['errmsg' => 'User Not Found'],406);
    }

    public function resellerLoginFromRoot(Request $request)
    {
        if((Auth::guard('root')->check() || Auth::guard('manager')->check()) && Reseller::where('email',$request->email)->exists())
        {
            $user = Reseller::where('email',$request->email)->first();
            
            if (Auth::guard('root')->check()) {
                Auth::guard('root')->logout();
            }

            if (Auth::guard('manager')->check()) {
                Auth::guard('manager')->logout();
            }
            Auth::guard('reseller')->login($user);
            return redirect()->route('reseller');
        }

        return response()->json(['errmsg' => 'User Not Found'],406);
    }


    /**
     * Manage Staff Activity
     *
     * @return void
     */
    public function staffActivity()
    {
        // DB table to use
        $table = 'staff_activity';

        // Table's primary key
        $primaryKey = 'activityid';

        $where = " activity_name != 'Product Sale'";

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes

        
        $columns = array(
            array( 'db' => 'activityid', 'dt' => 0 ),
            array( 'db' => 'name', 'dt' => 1 ),
            array( 'db' => 'activity_name',  'dt' => 2 ),
            array( 'db' => 'activity_type',   'dt' => 3 ),
            array( 'db' => 'activity_desc',   'dt' => 4 ),
            array( 'db' => 'record_id',   'dt' => 5 ),
            array( 'db' => 'created_at',   'dt' => 6 )
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

        

        echo json_encode(DataTableClass::complex( $_GET, $sql_details, $table, $primaryKey, $columns,null, $where));
    }

    /**
     * Manage Staff Activity
     *
     * @return void
     */
    public function staffInvoiceActivity()
    {
        // DB table to use
        $table = 'staff_activity';

        // Table's primary key
        $primaryKey = 'activityid';

        $where = " activity_name = 'Product Sale'";

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes

        
        $columns = array(
            array( 'db' => 'activityid', 'dt' => 0 ),
            array( 'db' => 'name', 'dt' => 1 ),
            array( 'db' => 'activity_name',  'dt' => 2 ),
            array( 'db' => 'activity_type',   'dt' => 3 ),
            array( 'db' => 'activity_desc',   'dt' => 4 ),
            array( 'db' => 'record_id',   'dt' => 5 ),
            array( 'db' => 'invoice_val',   'dt' => 6 ),
            array( 'db' => 'created_at',   'dt' => 7 )
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

}
