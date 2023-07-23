<?php

namespace App\Http\Controllers;

use App\Core\Accounts\Accounts;
use App\Core\AccountsChart\AccountsHead;
use App\Core\ProductSales\ProductSales;
use App\Core\UserCountSms\UserCountSms;
use App\Http\Requests\AccountTransectionRequest;
use App\Http\Requests\ProductSaleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\AccountTransaction;

class ResellerProductSaleToClientController extends Controller
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
     * 
     */
    protected $acchead;

    protected $smssent;

    public function __construct(
        ProductSales $productsale,
        UserCountSms $smssent,
        Accounts $account,
        AccountsHead $acchead
    )
    {
        $this->middleware('auth:root,reseller,manager');
        $this->productsale = $productsale;
        $this->smssent = $smssent;
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

        $masktype = collect($request->dataarr)->where('sms_type','mask');
        $reseller = Auth::guard('reseller')->user();

        if (count($masktype) > 0) {
            foreach($masktype as $mask){
                
                if ($reseller->mask_balance < $mask['smsqty'] &&  $mask['sms_type'] == 'mask')
                {
                    return response()->json(['errmsg' => "Insufficient mask sms balance. Balance: ".$reseller->mask_balance], 406);
                }
            }
        }

        $nomasktype = collect($request->dataarr)->where('sms_type','nomask');

        if (count($nomasktype) > 0) {
            foreach($nomasktype as $nomask)
            {
                if ($reseller->nonmask_balance < $nomask['smsqty'] &&  $nomask['sms_type'] == 'nomask')
                {
                    return response()->json(['errmsg' => "Insufficient nonmask sms balance. Balance: " .$reseller->nonmask_balance ], 406);
                }
            }
        }

        
        $voicetype = collect($request->dataarr)->where('sms_type','voice');

        if (count($voicetype) > 0) {
            foreach($voicetype as $voice)
            {

                if ($reseller->voice_balance  < $voice['smsqty'] &&  $voice['sms_type'] == 'voice')
                {
                    return response()->json(['errmsg' => "Insufficient voice sms balance. Balance: ".$reseller->voice_balance], 406);
                }

            }
        }


        $trasectionid = Auth::guard('reseller')->user()->id.time().mt_rand(10,9999);
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
                    'invoice_owner_type' => 'reseller',
                    'invoice_owner_id' => Auth::guard('reseller')->user()->id,
                ]);

                $txnType = 3;//voice
                $txnTypeName = 'voice';
                if ($data['sms_type']=='mask') {
                    $txnType = 1;
                    $txnTypeName = 'mask';
                } else if($data['sms_type']=='nomask'){
                    $txnType = 2;
                    $txnTypeName = 'nonmask';
                }

                //process balance
                //invoice is created for user
                $clientData = User::find($client);
                $resellerData = Auth::guard('reseller')->user();
                $isReseller = 0;


                if ($paymentoption == 'cash') {
                    // credit invoice
                    //update balance client
                    if ($txnType == 1) {
                        $newBalance = $clientData->mask_balance + $data['smsqty'];
                        $clientData->mask_balance = $newBalance;
                    } else if ($txnType == 2) {
                        $newBalance = $clientData->nonmask_balance + $data['smsqty'];
                        $clientData->nonmask_balance = $newBalance;
                    } else if ($txnType == 3) {
                        $newBalance = $clientData->voice_balance + $data['smsqty'];
                        $clientData->voice_balance = $newBalance;
                    }
                    $clientData->save();

                    //update balance reseller
                    if ($txnType == 1) {
                        $newResellerBalance = $resellerData->mask_balance - $data['smsqty'];
                        $resellerData->mask_balance = $newResellerBalance;
                    } else if ($txnType == 2) {
                        $newResellerBalance = $resellerData->nonmask_balance - $data['smsqty'];
                        $resellerData->nonmask_balance = $newResellerBalance;
                    } else if ($txnType == 3) {
                        $newResellerBalance = $resellerData->voice_balance - $data['smsqty'];
                        $resellerData->voice_balance = $newResellerBalance;
                    }
                    $resellerData->save();

                    //client transaction
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

                    //reseller transaction
                    AccountTransaction::create([
                        'type'        => $txnType,
                        'user'        => $resellerData->id,
                        'is_reseller' => 1,
                        'txn_type'    => $txnTypeName.'_sellclient',
                        'reference'   => $invoice->id,
                        'debit'       => $data['smsqty'],
                        'credit'      => 0,
                        'balance'     => $newResellerBalance,
                        'note'        => 'Payment Invoice Created to Customer',
                        'active'      => 1,
                    ]);
                } else if ($paymentoption == 'debit') {
                    // debit invoice
                    //update balance client
                    if ($txnType == 1) {
                        $newBalance = $clientData->mask_balance - $data['smsqty'];
                        $clientData->mask_balance = $newBalance;
                    } else if ($txnType == 2) {
                        $newBalance = $clientData->nonmask_balance - $data['smsqty'];
                        $clientData->nonmask_balance = $newBalance;
                    } else if ($txnType == 3) {
                        $newBalance = $clientData->voice_balance - $data['smsqty'];
                        $clientData->voice_balance = $newBalance;
                    }
                    $clientData->save();

                    //update balance reseller
                    if ($txnType == 1) {
                        $newResellerBalance = $resellerData->mask_balance + $data['smsqty'];
                        $resellerData->mask_balance = $newResellerBalance;
                    } else if ($txnType == 2) {
                        $newResellerBalance = $resellerData->nonmask_balance + $data['smsqty'];
                        $resellerData->nonmask_balance = $newResellerBalance;
                    } else if ($txnType == 3) {
                        $newResellerBalance = $resellerData->voice_balance + $data['smsqty'];
                        $resellerData->voice_balance = $newResellerBalance;
                    }
                    $resellerData->save();
                    
                    //client transaction
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

                    //reseller transaction
                    AccountTransaction::create([
                        'type'        => $txnType,
                        'user'        => $resellerData->id,
                        'is_reseller' => 1,
                        'txn_type'    => $txnTypeName.'_returnclient',
                        'reference'   => $invoice->id,
                        'debit'       => 0,
                        'credit'      => $data['smsqty'],
                        'balance'     => $newResellerBalance,
                        'note'        => 'Return Invoice Created to Customer',
                        'active'      => 1,
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

            if ($clientreceivable && $paymentoption == 'cash')
            {
                $voucherid = Auth::guard('reseller')->user()->id.time().mt_rand(10,9999);

                $this->account->addVoucher([
                        'account_head_id' => $clientreceivable->id,
                        'account_parent_id' => $clientreceivable->parent_id,
                        'amount_dr' => $data['invoice_vat'] > 0 ? $totalWithoutvat : $totalWithVat,
                        'amount_cr' => 0,
                        'user_id' => $client,
                        'voucher_owner' => 'reseller',
                        'voucher_owner_id' => Auth::guard('reseller')->user()->id,
                        'voucher_id' => $voucherid,
                        'transection_id' => $trasectionid,
                        'voucher_date' => $voucherdate,
                        'voucher_create_origin' => 'reseller'
                ]);

                $this->account->addVoucher([
                        'account_head_id' => $smssale->id,
                        'account_parent_id' => $smssale->parent_id,
                        'amount_dr' => 0,
                        'amount_cr' => $totalWithVat,
                        'user_id' => $client,
                        'voucher_owner' => 'reseller',
                        'voucher_owner_id' => Auth::guard('reseller')->user()->id,
                        'voucher_id' => $voucherid,
                        'transection_id' => $trasectionid,
                        'voucher_date' => $voucherdate,
                        'voucher_create_origin' => 'reseller'
                ]);


                if ($data['invoice_vat'] > 0)
                {
                    $this->account->addVoucher([
                            'account_head_id' => $vatreceivable->id,
                            'account_parent_id' => $vatreceivable->parent_id,
                            'amount_dr' => $vatamount,
                            'amount_cr' => 0,
                            'user_id' => $client,
                            'voucher_owner' => 'reseller',
                            'voucher_owner_id' => Auth::guard('reseller')->user()->id,
                            'voucher_id' => $voucherid,
                            'transection_id' => $trasectionid,
                            'voucher_date' => $voucherdate,
                            'voucher_create_origin' => 'reseller'
                    ]);

                    $this->account->addVoucher([
                            'account_head_id' => $vatpayable->id,
                            'account_parent_id' => $vatpayable->parent_id,
                            'amount_dr' => 0,
                            'amount_cr' => $vatamount,
                            'user_id' => $client,
                            'voucher_owner' => 'reseller',
                            'voucher_owner_id' => Auth::guard('reseller')->user()->id,
                            'voucher_id' => $voucherid,
                            'transection_id' => $trasectionid,
                            'voucher_date' => $voucherdate,
                            'voucher_create_origin' => 'reseller'
                    ]);

                    $this->account->addVoucher([
                            'account_head_id' => $vatexpense->id,
                            'account_parent_id' => $vatexpense->parent_id,
                            'amount_dr' => $vatamount,
                            'amount_cr' => 0,
                            'user_id' => $client,
                            'voucher_owner' => 'reseller',
                            'voucher_owner_id' => Auth::guard('reseller')->user()->id,
                            'voucher_id' => $voucherid,
                            'transection_id' => $trasectionid,
                            'voucher_date' => $voucherdate,
                            'voucher_create_origin' => 'reseller'
                    ]);
                }

                if ($paymentoption == 'cash')
                {
                    $voucherid = Auth::guard('reseller')->user()->id.time().mt_rand(10,9999);

                    if ($data['invoice_vat'] > 0) {
                        $this->account->addVoucher([
                                'account_head_id' => $vatreceivable->id,
                                'account_parent_id' => $vatreceivable->parent_id,
                                'amount_dr' => 0,
                                'amount_cr' => $vatamount,
                                'user_id' => $client,
                                'voucher_owner' => 'reseller',
                                'voucher_owner_id' => Auth::guard('reseller')->user()->id,
                                'voucher_id' => $voucherid,
                                'transection_id' => $trasectionid,
                                'voucher_date' => $voucherdate,
                                'voucher_create_origin' => 'reseller'
                        ]);
                    }

                    $this->account->addVoucher([
                            'account_head_id' => $clientreceivable->id,
                            'account_parent_id' => $clientreceivable->parent_id,
                            'amount_dr' => 0,
                            'amount_cr' => $totalWithoutvat,
                            'user_id' => $client,
                            'voucher_owner' => 'reseller',
                            'voucher_owner_id' => Auth::guard('reseller')->user()->id,
                            'voucher_id' => $voucherid,
                            'transection_id' => $trasectionid,
                            'voucher_date' => $voucherdate,
                            'voucher_create_origin' => 'reseller'
                    ]);

                    $this->account->addVoucher([
                            'account_head_id' => $cashorbank->id,
                            'account_parent_id' => $cashorbank->parent_id,
                            'amount_dr' => $data['invoice_vat'] > 0 ? ($totalWithoutvat+$vatamount) : $totalWithVat,
                            'amount_cr' => 0,
                            'user_id' => $client,
                            'voucher_owner' => 'reseller',
                            'voucher_owner_id' => Auth::guard('reseller')->user()->id,
                            'voucher_id' => $voucherid,
                            'transection_id' => $trasectionid,
                            'voucher_date' => $voucherdate,
                            'voucher_create_origin' => 'reseller'
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
    public function resellerMyInvoiceList() {

        return view('smsview.smssale.reseller-my-invoice-list');

    }

    public function showMyResellerInvoices(Request $request)
    {
        $data = [
            'userid' => Auth::guard('reseller')->check() ? Auth::guard('reseller')->user()->id : @$request->userid,
        ];
        return $this->productsale->showMyResellerInvoices($data);
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
