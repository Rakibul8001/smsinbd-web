<?php

namespace App\Http\Controllers;

use App\Core\AccountsChart\AccountsHead;
use App\Core\AccountsChart\AccountsHeadDetails;
use App\Core\Users\ClientInterface;
use App\Core\Users\ClientRepository;
use App\Core\Users\ResellerInterface;
use App\Core\Users\ResellerRepository;
use App\Core\UserCountSms\UserCountSms;
use App\Core\ProductSales\ProductSales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResellerSmsSaleToClientController extends Controller
{
    /**
     * Client service
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
     * Account chart service
     *
     * @var App\Core\AccountsChart\AccountsHeadDetails
     */
    protected $accountshead;

    /**
     * Product sales service
     *
     * @var App\Core\ProductSales\ProductSaleDetails
     */
    protected $productsale;

    /**
     * SMS send count service
     *
     * @var App\Core\UserCountSms\UserCountSms;
     */
    protected $smssent;


    public function __construct(
        ClientInterface $client,
        ResellerInterface $reseller,
        AccountsHead $accountshead,
        ProductSales $productsale,
        UserCountSms $smssent
    )
    {
        $this->middleware('auth:root,reseller');

        $this->client = $client;
        $this->reseller = $reseller;
        $this->accountshead = $accountshead;
        $this->productsale = $productsale;
        $this->smssent = $smssent;
        
    }

    public function resellerSmsBalanceCheck(Request $request)
    {

        $senderidtype = $request->sms_type;
        $totalSms = $request->smsqty;


        $reseller = Auth::guard('reseller')->user();

        if ($reseller->mask_balance < $totalSms && $senderidtype == 'mask')
        {
            return response()->json(['errmsg' => "Insufficient mask sms balance. Balance: ".$reseller->mask_balance], 406);
        }

        if ($reseller->nonmask_balance < $totalSms && $senderidtype == 'nomask')
        {
            return response()->json(['errmsg' => "Insufficient nonmask sms balance. Balance: " .$reseller->nonmask_balance], 406);
        }

        if ($reseller->voice_balance < $totalSms && $senderidtype == 'voice')
        {
            return response()->json(['errmsg' => "Insufficient voice sms balance. Balance: ".$reseller->voice_balance], 406);
        }

        return true;
    }

    public function smsSale()
    {
        if (! $this->client instanceof ClientRepository)
        {
            return back()->with('errmsg','Client must be an instance of ClientRepository');
        }

        if (! $this->accountshead instanceof AccountsHeadDetails) 
        {
            return back()->with('errmsg','Account must be an instance of AccountsHeadDetails');
        }
        
        if(Auth::guard('reseller')->check())
        {
            $resellerid = Auth::guard('reseller')->user()->id;

            $clients = $this->client->activeResellerClients($resellerid);
        } else {

            $clients = $this->client->activeClients();

        }

        $groups = $this->accountshead->getAllGroupAccountsHeadById(5);

        return view('smsview.smssale.sms-sale-form',compact('clients','groups'));
    }

    public function smsSaleToReseller()
    {
        if (! $this->reseller instanceof ResellerRepository)
        {
            return back()->with('errmsg','Reseller must be an instance of ResellerRepository');
        }

        if (! $this->accountshead instanceof AccountsHeadDetails) 
        {
            return back()->with('errmsg','Account must be an instance of AccountsHeadDetails');
        }
        
        $clients = $this->reseller->activeResellers();
        $groups = $this->accountshead->getAllGroupAccountsHeadById(5);
        return view('smsview.smssale.reseller-sms-sale-form',compact('clients','groups'));
    }
}
