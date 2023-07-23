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

class TestController extends Controller
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



    /**
       * Client sms sent service
       *
       * @var App\Core\UserCountSms\UserCountSmsDetails
       */
      protected $smssent;


    public function __construct(
        CountriesInterface $country,
        ClientDocumentsInterface $clientDocument,
        UserCountSms $consumesms,
        ProductSales $product,
        BalanceReconciliation $userbalance,
        AccountsHead $accountshead

    )
    {

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
    
    

    public function clientBalance(Request $request)
    {
        
        $clientid = $request->userid;
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


    public function resellerBalance(Request $request)
    {
        $resellerid = !empty($request->userid) ? $request->userid : Auth::guard('reseller')->user()->id;
        $totdaymasksentbal = $this->consumesms->resellerClientConsumeMaskSmsBalance($resellerid);
        $todaynonmasksentbal = $this->consumesms->resellerClientConsumeNonMaskSmsBalance($resellerid);
        $todayvoicesentbal = $this->consumesms->resellerClientConsumeVoiceSmsBalance($resellerid);

        $totalsentsms = ($totdaymasksentbal+$todaynonmasksentbal+$todayvoicesentbal);

        $thisweeksentsmsbal = $this->consumesms->resellerClientThisWeekConsumeMaskSmsBalance($resellerid);
        $thismonthsentsmsbal = $this->consumesms->resellerClientThisMonthConsumeMaskSmsBalance($resellerid);


        $clientmasksmsbal = $this->product->getResellerSmsBalanceByCategory($resellerid,'mask');
        $clientnonmasksmsbal = $this->product->getResellerSmsBalanceByCategory($resellerid,'nomask');
        $clientvoicemsbal = $this->product->getResellerSmsBalanceByCategory($resellerid,'voice');

        $totalmaskbal = ($clientmasksmsbal-$this->consumesms->resellerTotalConsumeMaskBalance($resellerid));
        $totalnonmaskbal = ($clientnonmasksmsbal-$this->consumesms->resellerTotalConsumeNonMaskBalance($resellerid));
        $totalvoicebal = ($clientvoicemsbal-$this->consumesms->resellerTotalConsumeVoiceBalance($resellerid));

        return response()->json(['balance' => [
            'maskbalance' => $totalmaskbal,
            'nonmaskbalance' => $totalnonmaskbal,
            'voicebalance' => $totalvoicebal
        ]], 200);
    }

}
