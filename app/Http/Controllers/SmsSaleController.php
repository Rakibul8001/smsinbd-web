<?php

namespace App\Http\Controllers;

use App\Core\AccountsChart\AccountsHead;
use App\Core\AccountsChart\AccountsHeadDetails;
use App\Core\Users\ClientInterface;
use App\Core\Users\ClientRepository;
use App\Core\Users\ResellerInterface;
use App\Core\Users\ResellerRepository;
use Illuminate\Http\Request;

class SmsSaleController extends Controller
{
    /**
     * Client service
     *
     * @var App\Core\Users\ClientRepository
     */
    protected $client;

    protected $reseller;

    protected $accountshead;


    public function __construct(
        ClientInterface $client,
        ResellerInterface $reseller,
        AccountsHead $accountshead
    )
    {
        $this->middleware('auth:root,manager');

        $this->client = $client;
        $this->reseller = $reseller;
        $this->accountshead = $accountshead;
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
        
        $clients = $this->client->activeClients();
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
