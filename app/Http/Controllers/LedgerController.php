<?php

namespace App\Http\Controllers;

use App\Core\Users\ClientInterface;
use App\Http\Resources\UserResource;
use App\Models\Users;
use App\User;
use App\Reseller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LedgerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:root,client');
    }

    public function customerSelection()
    {

        $customers = User::all();

        return view('smsview.ledger.root-selectCustomer',compact('customers'));
    }


    public function rootCustomerLedgerDetails(Request $request)
    {

        if (!$request->customer) {
            return back()->with(['errmsg' => 'Please select a customer']);
        }

        $client = User::find($request->customer);

        $totalPurchasedSms = DB::select("select SUM(credit-debit) AS total_purchased, type  from account_transactions where user =$client->id AND note IN ('Payment Invoice Created', 'Return Invoice Created') GROUP BY type");



        $totalSingleSms = DB::select("SELECT SUM(qty) total_sent, category as type from sms_individuals where user_id=$client->id status NOT IN(0,3) GROUP BY category");

        $totalSingleSmsFailed = DB::select("SELECT SUM(qty) total_failed, category as type from sms_individuals where user_id=$client->id AND status IN(0,3) GROUP BY category");

        $campaignSmsData = "SELECT category, sum(sms_qty*total_numbers)  total_sent, 
        SUM(CASE
            WHEN sms_campaigns.campaign_type='A' THEN (SELECT COUNT(sms_campaign_numbers.id) from sms_campaign_numbers where sms_campaign_numbers.campaign_id= sms_campaigns.id AND (sms_campaign_numbers.status=3 OR sms_campaign_numbers.status=0)) *sms_campaigns.sms_qty
            
            WHEN sms_campaigns.campaign_type='B' THEN (SELECT COUNT(sms_campaign_numbersB.id) from sms_campaign_numbersB where sms_campaign_numbersB.campaign_id= sms_campaigns.id AND (sms_campaign_numbersB.status=3 OR sms_campaign_numbersB.status=0)) *sms_campaigns.sms_qty
            
            WHEN sms_campaigns.campaign_type='C' THEN (SELECT COUNT(sms_campaign_numbersC.id) from sms_campaign_numbersC where sms_campaign_numbersC.campaign_id= sms_campaigns.id AND (sms_campaign_numbersC.status=3 OR sms_campaign_numbersC.status=0)) *sms_campaigns.sms_qty
            
            WHEN sms_campaigns.campaign_type='D' THEN (SELECT COUNT(sms_campaign_numbersD.id) from sms_campaign_numbersD where sms_campaign_numbersD.campaign_id= sms_campaigns.id AND (sms_campaign_numbersD.status=3 OR sms_campaign_numbersD.status=0)) *sms_campaigns.sms_qty
            
            WHEN sms_campaigns.campaign_type='E' THEN (SELECT COUNT(sms_campaign_numbersE.id) from sms_campaign_numbersE where sms_campaign_numbersE.campaign_id= sms_campaigns.id AND (sms_campaign_numbersE.status=3 OR sms_campaign_numbersE.status=0)) *sms_campaigns.sms_qty
            END) AS total_failed
         from sms_campaigns where user_id=$client->id group by category";

        $maskPurchase = $nonmaskPurchase = $maskTotalSent = $nonmaskTotalSent = $nonmaskCampaignTotalSent = $maskCampaignTotalSent = 0 ;
        foreach ($totalPurchasedSms as $key => $data) {
            if($data->type==1){
                $maskPurchase = $data->total_purchased;
            }
            if($data->type==2){
                $nonmaskPurchase = $data->total_purchased;
            }
        }

        foreach ($totalSingleSms as $key => $data) {
            if($data->type==1){
                $maskTotalSent = $data->total_sent;
            }
            if($data->type==2){
                $nonmaskTotalSent = $data->total_sent;
            }
        }
        foreach($totalSingleSmsFailed as $data){
            if($data->type==1){
                $maskSingleFailed = $data->total_failed;
            }
            if($data->type==2){
                $nonmaskSingleFailed  = $data->total_failed;
            }
        }

        foreach($campaignSmsData as $key => $data){
            if($data->category==1){
                $maskCampaignTotalSent = $data->total_sent;
                $maskCampaignTotalFailed = $data->total_failed;                
            }
            if($data->category==2){
                $nonmaskCampaignTotalSent = $data->total_sent;
                $nonmaskCampaignTotalFailed = $data->total_failed;                
            } 
        }

        $totalData['maskPurchase'] = $maskPurchase;
        $totalData['nonmaskPurchase'] = $nonmaskPurchase;
        $totalData['maskSent'] = $maskSent+$maskCampaignTotalSent;
        $totalData['maskSent'] = $maskTotalSent+$maskCampaignTotalSent;
        $totalData['nonmaskSent'] = $nonmaskTotalSent+$nonmaskCampaignTotalSent;
        $totalData['maskFailed'] = $maskSingleFailed+$maskCampaignTotalFailed;
        $totalData['nonmaskFailed'] = $nonmaskSingleFailed+$nonmaskCampaignTotalFailed;


        print_r($totalData); exit;


        return back()->with(['msg' => 'Sender ID created successfully']);
    }

}
