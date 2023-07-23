<?php

namespace App\Http\Controllers;

use App\Core\Accounts\Accounts;
use App\Core\AccountsChart\AccountsHead;
use App\Core\ProductSales\ProductSales;
use App\Core\UserCountSms\UserCountSms;
use App\Http\Requests\AccountTransectionRequest;
use App\Http\Requests\ProductSaleRequest;
use Illuminate\Http\Request;
use App\User;
use App\Reseller;
use App\AccountTransaction;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProductSaleController extends Controller
{
    /**
     * Product sale service
     *
     * @var App\Core\ProductSale\ProductSaleDetails
     */
    protected $productsale;
    
    /**
     * Account's serive
     *
     * @var App\Core\Accounts\AccountDetails
     */
    protected $account;

    /**
     * Account's head service
     *
     * @var App\Core\AccountsChart\AccountsHeadDetails
     */
    protected $acchead;

    protected $smssent;

    public function __construct(
        ProductSales $productsale,
        Accounts $account,
        AccountsHead $acchead,
        UserCountSms $smssent
    )
    {
        $this->middleware('auth:root,manager');
        $this->productsale = $productsale;
        $this->account = $account;        
        $this->acchead = $acchead;
    }

    //AccountTransectionRequest $accrequest
    public function addSmsSale(
        Request $request
    ){

        $vatamount = 0;
        $totalWithVat = 0;
        $totalWithoutvat = 0;
        $client = '';
        $voucherdate = '';
        $invoicevat = 0;
        $paymentoption = '';
        $payby = '';
        $a = 0;

        if (Auth::guard('root')->check()) {
            $trasectionid = @Auth::guard('root')->user()->id.time().mt_rand(10,9999);
        } else {
            $trasectionid = @Auth::guard('manager')->user()->id.time().mt_rand(10,9999);
        }
        if (count($request->dataarr) > 0)
        {
            
            foreach($request->dataarr as $data)
            {
                $vatamount = ($data['invoice_vat']/100)*collect($request->dataarr)->sum('price');
                $totalWithVat = collect($request->dataarr)->sum('price');
                $totalWithoutvat = ($totalWithVat-$vatamount);

                $paymentoption = $data['paymentoption'];

                $client = $data['client'];

                $voucherdate = $data['invoice_date'];

                $invoicevat = $data['invoice_vat'];

                $payby = $data['paymentby'];

                $invoice = $this->productsale->addInvoiceProduct([
                    'user_id' => $data['client'],
                    'user_type' => $data['user_type'],
                    'transection_id' => $trasectionid,
                    'sms_category' => $data['sms_type'],
                    'qty' => $paymentoption == 'cash' ? $data['smsqty'] : 0,
                    'qty_return' => $paymentoption == 'debit' ? $data['smsqty']: 0,
                    'rate' => $data['rate'],
                    'price' => $data['price'],
                    'validity_period' => $data['validity_date'],
                    'invoice_vat' => $data['invoice_vat'],
                    'vat_amount' => $vatamount,
                    'invoice_date' => $data['invoice_date'],
                    'invoice_owner_type' => 'root',
                    'invoice_owner_id' => Auth::guard('root')->check() ? Auth::guard('root')->user()->id : 1,
                ]);

                $txnType = 3;//voice
                $txnTypeName = 'voice';
                if ($data['sms_type']=='mask') {
                    $txnType = 1;
                    $txnTypeName = 'mask';
                } else if($data['sms_type']=='nomask'){
                    $txnType = 2;
                    $txnTypeName = 'nonmask';
                } else if($data['sms_type']=='lc'){
                    $txnType = 4;
                    $txnTypeName = 'lc';
                }

                //process balance
                $isReseller = 0;
                if ($data['user_type'] == 'user') {
                    //invoice is created for user
                    $clientData = User::find($client);
                    $isReseller = 0;

                } else if ($data['user_type']=='reseller') {
                    //invoice is created for reseller
                    $clientData = Reseller::find($client);
                    $isReseller = 1;
                }

                if ($paymentoption == 'cash') {
                    // credit invoice
                    //update balance
                    if ($txnType == 1) {
                        $newBalance = $clientData->mask_balance + $data['smsqty'];
                        $clientData->mask_balance = $newBalance;
                    } else if ($txnType == 2) {
                        $newBalance = $clientData->nonmask_balance + $data['smsqty'];
                        $clientData->nonmask_balance = $newBalance;
                    } else if ($txnType == 3) {
                        $newBalance = $clientData->voice_balance + $data['smsqty'];
                        $clientData->voice_balance = $newBalance;
                    } else if ($txnType == 4) {
                        $newBalance = $clientData->lowcost_balance + $data['smsqty'];
                        $clientData->lowcost_balance = $newBalance;
                    }
                    $clientData->save();

                    AccountTransaction::create([
                        'type'        => $txnType,
                        'user'        => $client,
                        'is_reseller' => $isReseller,
                        'txn_type'    => $txnTypeName.'_sell',
                        'reference'   => $invoice->id,
                        'debit'       => 0,
                        'credit'      => $data['smsqty'],
                        'balance'     => $newBalance,
                        'note'        => 'Payment Invoice Created',
                        'active'      => 1,
                    ]);
                } else if ($paymentoption == 'debit') {
                    // debit invoice
                    //update balance
                    if ($txnType == 1) {
                        $newBalance = $clientData->mask_balance - $data['smsqty'];
                        $clientData->mask_balance = $newBalance;
                    } else if ($txnType == 2) {
                        $newBalance = $clientData->nonmask_balance - $data['smsqty'];
                        $clientData->nonmask_balance = $newBalance;
                    } else if ($txnType == 3) {
                        $newBalance = $clientData->voice_balance - $data['smsqty'];
                        $clientData->voice_balance = $newBalance;
                    } else if ($txnType == 4) {
                        $newBalance = $clientData->lowcost_balance - $data['smsqty'];
                        $clientData->lowcost_balance = $newBalance;
                    }
                    $clientData->save();
                    
                    AccountTransaction::create([
                        'type'        => $txnType,
                        'user'        => $client,
                        'is_reseller' => $isReseller,
                        'txn_type'    => $txnTypeName.'_return',
                        'reference'   => $invoice->id,
                        'debit'       => $data['smsqty'],
                        'credit'      => 0,
                        'balance'     => $newBalance,
                        'note'        => 'Return Invoice Created',
                        'active'      => 1,
                    ]);
                }

                if (Auth::guard('manager')->check()) {

                    $manager = Auth::guard('manager')->user()->name;
    
                    $edituser = $data['user_type'] == 'user' ? User::where('id', $client)->first() : Reseller::where('id', $client)->first();
                    $activity_desc = $data['user_type'] == 'user' ? "Manager {$manager} created invoice for client {$edituser->name}/{$edituser->email}/{$edituser->phone} and invoiceid is {$trasectionid}" : "Manager {$manager} created invoice for reseller {$edituser->name}/{$edituser->email}/{$edituser->phone} and invoiceid is {$trasectionid}"; 
    
                    DB::table("staff_activities")
                        ->insert([
                            'manager_id' => Auth::guard('manager')->user()->id,
                            'activity_name' => 'Product Sale',
                            'activity_type' => 'Insert',
                            'activity_desc' => $activity_desc,
                            'record_id' => $invoice->id,
                            'invoice_val' => $totalWithVat,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
    
                }
            }

            $clientreceivable = $this->acchead->getAccountsHeadById(25);
            $smssale = $this->acchead->getAccountsHeadById(22);
            $vatreceivable = $this->acchead->getAccountsHeadById(26);
            $vatpayable = $this->acchead->getAccountsHeadById(24);
            $vatexpense = $this->acchead->getAccountsHeadById(28);
            $cashorbank = $this->acchead->getAccountsHeadById($payby);

            $clientpayable = $this->acchead->getAccountsHeadById(30);
            $returnexp = $this->acchead->getAccountsHeadById(31);

            if (Auth::guard('root')->check()) {

                $voucherid = Auth::guard('root')->user()->id.time().mt_rand(10,9999);

            } else {

                $voucherid = Auth::guard('manager')->user()->id.time().mt_rand(10,9999);
                
            }

            if ($clientreceivable && $paymentoption == 'cash')
            {
                //$voucherid = Auth::guard('root')->user()->id.time().mt_rand(10,9999);
                $this->account->addVoucher([
                        'account_head_id' => $clientreceivable->id,
                        'account_parent_id' => $clientreceivable->parent_id,
                        'amount_dr' => $data['invoice_vat'] > 0 ? $totalWithoutvat : $totalWithVat,
                        'amount_cr' => 0,
                        'user_id' => $client,
                        'voucher_owner' => 'root',
                        'voucher_owner_id' => Auth::guard('root')->check() ? Auth::guard('root')->user()->id : 1,
                        'voucher_id' => $voucherid,
                        'transection_id' => $trasectionid,
                        'voucher_date' => $voucherdate,
                        'voucher_create_origin' => 'root'
                ]);

                $this->account->addVoucher([
                        'account_head_id' => $smssale->id,
                        'account_parent_id' => $smssale->parent_id,
                        'amount_dr' => 0,
                        'amount_cr' => $totalWithVat,
                        'user_id' => $client,
                        'voucher_owner' => 'root',
                        'voucher_owner_id' => Auth::guard('root')->check() ? Auth::guard('root')->user()->id : 1,
                        'voucher_id' => $voucherid,
                        'transection_id' => $trasectionid,
                        'voucher_date' => $voucherdate,
                        'voucher_create_origin' => 'root'
                ]);


                if ($data['invoice_vat'] > 0)
                {
                    $this->account->addVoucher([
                            'account_head_id' => $vatreceivable->id,
                            'account_parent_id' => $vatreceivable->parent_id,
                            'amount_dr' => $vatamount,
                            'amount_cr' => 0,
                            'user_id' => $client,
                            'voucher_owner' => 'root',
                            'voucher_owner_id' => Auth::guard('root')->check() ? Auth::guard('root')->user()->id : 1,
                            'voucher_id' => $voucherid,
                            'transection_id' => $trasectionid,
                            'voucher_date' => $voucherdate,
                            'voucher_create_origin' => 'root'
                    ]);

                    $this->account->addVoucher([
                            'account_head_id' => $vatpayable->id,
                            'account_parent_id' => $vatpayable->parent_id,
                            'amount_dr' => 0,
                            'amount_cr' => $vatamount,
                            'user_id' => $client,
                            'voucher_owner' => 'root',
                            'voucher_owner_id' => Auth::guard('root')->check() ? Auth::guard('root')->user()->id : 1,
                            'voucher_id' => $voucherid,
                            'transection_id' => $trasectionid,
                            'voucher_date' => $voucherdate,
                            'voucher_create_origin' => 'root'
                    ]);

                    $this->account->addVoucher([
                            'account_head_id' => $vatexpense->id,
                            'account_parent_id' => $vatexpense->parent_id,
                            'amount_dr' => $vatamount,
                            'amount_cr' => 0,
                            'user_id' => $client,
                            'voucher_owner' => 'root',
                            'voucher_owner_id' => Auth::guard('root')->check() ? Auth::guard('root')->user()->id : 1,
                            'voucher_id' => $voucherid,
                            'transection_id' => $trasectionid,
                            'voucher_date' => $voucherdate,
                            'voucher_create_origin' => 'root'
                    ]);
                }

                if ($paymentoption == 'cash')
                {
                    if ($data['invoice_vat'] > 0) {
                        $this->account->addVoucher([
                                'account_head_id' => $vatreceivable->id,
                                'account_parent_id' => $vatreceivable->parent_id,
                                'amount_dr' => 0,
                                'amount_cr' => $vatamount,
                                'user_id' => $client,
                                'voucher_owner' => 'root',
                                'voucher_owner_id' => Auth::guard('root')->check() ? Auth::guard('root')->user()->id : 1,
                                'voucher_id' => $voucherid,
                                'transection_id' => $trasectionid,
                                'voucher_date' => $voucherdate,
                                'voucher_create_origin' => 'root'
                        ]);
                    }
                    $this->account->addVoucher([
                            'account_head_id' => $clientreceivable->id,
                            'account_parent_id' => $clientreceivable->parent_id,
                            'amount_dr' => 0,
                            'amount_cr' => $totalWithoutvat,
                            'user_id' => $client,
                            'voucher_owner' => 'root',
                            'voucher_owner_id' => Auth::guard('root')->check() ? Auth::guard('root')->user()->id : 1,
                            'voucher_id' => $voucherid,
                            'transection_id' => $trasectionid,
                            'voucher_date' => $voucherdate,
                            'voucher_create_origin' => 'root'
                    ]);

                    $this->account->addVoucher([
                            'account_head_id' => $cashorbank->id,
                            'account_parent_id' => $cashorbank->parent_id,
                            'amount_dr' => $data['invoice_vat'] > 0 ? ($totalWithoutvat+$vatamount) : $totalWithVat,
                            'amount_cr' => 0,
                            'user_id' => $client,
                            'voucher_owner' => 'root',
                            'voucher_owner_id' => Auth::guard('root')->check() ? Auth::guard('root')->user()->id : 1,
                            'voucher_id' => $voucherid,
                            'transection_id' => $trasectionid,
                            'voucher_date' => $voucherdate,
                            'voucher_create_origin' => 'root'
                    ]);
                }
            } else {

                //$voucherid = Auth::guard('root')->user()->id.time().mt_rand(10,9999);
                $this->account->addVoucher([
                        'account_head_id' => $clientpayable->id,
                        'account_parent_id' => $clientpayable->parent_id,
                        'amount_dr' => 0,
                        'amount_cr' => $totalWithoutvat,
                        'user_id' => $client,
                        'voucher_owner' => 'root',
                        'voucher_owner_id' => Auth::guard('root')->check() ? Auth::guard('root')->user()->id : 1,
                        'voucher_id' => $voucherid,
                        'transection_id' => $trasectionid,
                        'voucher_date' => $voucherdate,
                        'voucher_create_origin' => 'root'
                ]);

                $this->account->addVoucher([
                        'account_head_id' => $smssale->id,
                        'account_parent_id' => $smssale->parent_id,
                        'amount_dr' => $totalWithVat,
                        'amount_cr' => 0,
                        'user_id' => $client,
                        'voucher_owner' => 'root',
                        'voucher_owner_id' => Auth::guard('root')->check() ? Auth::guard('root')->user()->id : 1,
                        'voucher_id' => $voucherid,
                        'transection_id' => $trasectionid,
                        'voucher_date' => $voucherdate,
                        'voucher_create_origin' => 'root'
                ]);


                if ($paymentoption == 'debit')
                {
                    //$voucherid = Auth::guard('root')->user()->id.time().mt_rand(10,9999);

                    $this->account->addVoucher([
                            'account_head_id' => $clientpayable->id,
                            'account_parent_id' => $clientpayable->parent_id,
                            'amount_dr' => $totalWithoutvat,
                            'amount_cr' => 0,
                            'user_id' => $client,
                            'voucher_owner' => 'root',
                            'voucher_owner_id' => Auth::guard('root')->check() ? Auth::guard('root')->user()->id : 1,
                            'voucher_id' => $voucherid,
                            'transection_id' => $trasectionid,
                            'voucher_date' => $voucherdate,
                            'voucher_create_origin' => 'root'
                    ]);

                    $this->account->addVoucher([
                            'account_head_id' => $cashorbank->id,
                            'account_parent_id' => $cashorbank->parent_id,
                            'amount_dr' => 0,
                            'amount_cr' => $totalWithoutvat,
                            'user_id' => $client,
                            'voucher_owner' => 'root',
                            'voucher_owner_id' => Auth::guard('root')->check() ? Auth::guard('root')->user()->id : 1,
                            'voucher_id' => $voucherid,
                            'transection_id' => $trasectionid,
                            'voucher_date' => $voucherdate,
                            'voucher_create_origin' => 'root'
                    ]);
                }
            }

            return response()->json(['msg' => 'Invoice successfully inserted'],200);
        }

        return response()->json(['errmsg' => 'Empty record set'], 406);
    }

    /**
     * Root and manager invoice list
     *
     * @return void
     */
    public function rootAndManagerInvoiceList() {

        return view('smsview.smssale.root-and-manager-invoicelist');

    }

    public function showRootClientInvoices()
    {
        if (Auth::guard('manager')->check()) {
            $data = [
                'invoice_owner_type' => 'root',
            ];
            return $this->productsale->showRootClientInvoices($data);
        }

        $data = [
            'invoice_owner_type' => Auth::guard('root')->check() ? 'root' : 'reseller',
            'invoice_owner_id' => Auth::guard('root')->check() ? Auth::guard('root')->user()->id : Auth::guard('reseller')->user()->id
        ];
        return $this->productsale->showRootClientInvoices($data);
    }


    /**
     * Root and manager invoice list
     *
     * @return void
     */
    public function resellerInvoiceList() {

        return view('smsview.smssale.reseller-invoicelist');

    }

    public function showResellerInvoices()
    {
        return $this->productsale->showResellerInvoices();
    }
}
