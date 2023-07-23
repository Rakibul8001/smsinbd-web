<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Core\AccountsChart\AccountsHead;
use App\Http\Requests\AccountsHeadRequest;

class AccountHeadController extends Controller
{
    
    /**
     * Accounts head service
     *
     * @var App\Core\AccountsChart\AccountsHeadDetails
     */
    protected $accountshead;

    public function __construct(AccountsHead $accountshead)
    {
        $this->middleware('auth:root');
        $this->accountshead = $accountshead;
    }

    //add-accounts-head
    public function addAccountsRootHead(AccountsHeadRequest $request)
    {
        $formmodearr = ['ins','edt'];
        $formmode = $request->accrootfrmmode;

        $acctype = ['parent','group','transection'];
        $requestacctype = $request->account_type;

        if (! in_array($formmode, $formmodearr) || ! in_array($requestacctype, $acctype))
        {
            return back()->with('errmsg','Form submission mode/account type not specified');
        }

        switch($formmode)
        {
            case 'ins':
                $this->accountshead->addAccountsHead([
                    'acc_head' => $request->acc_head,
                    'parent_id' => !empty($request->account_rec_id) ? $request->account_rec_id : 0,
                    'status' => $request->status,
                    'created_by' => Auth::guard('root')->user()->id,
                    'updated_by' => Auth::guard('root')->user()->id,
                    'user_type' => 'root',
                    'account_type' => $request->account_type,
                ]);

                return back()->with('msg','Account\'s successfully created');
                break;
            case 'edt':
                $this->updateAccountsHead($request);
                return back()->with('msg', 'Account\'s successfully updated');
                break;
            default:
                return back()->with('errmsg','Form submission mode not specified');
        }
    }

    /**
     * Account Head list
     *
     * @return void
     */
    public function showAccountsHead()
    {
        return view('smsview.accountshead.accounts-head');
    }

    /**
     * Get All root accounts
     *
     * @return void
     */
    public function renderRootAccountsHead()
    {
        return $this->accountshead->getAllRootAccountsHead();
    }


    /**
     * Group Account Head list
     *
     * @return void
     */
    public function showGroupAccountsHead()
    {
        return view('smsview.accountshead.group-accounts-head');
    }

    /**
     * Get All group accounts
     *
     * @return void
     */
    public function renderGroupAccountsHead()
    {
        return $this->accountshead->getAllGroupAccountsHead();
    }


    /**
     * Group Account Head list
     *
     * @return void
     */
    public function showTransectionAccountsHead()
    {
        return view('smsview.accountshead.bottom-accounts-head');
    }

    /**
     * Get All group accounts
     *
     * @return void
     */
    public function renderTransectionAccountsHead()
    {
        return $this->accountshead->getAllTransectionAccountsHead();
    }

    /**
     * Upate an account's head
     *
     * @param [type] $request
     * @return void
     */
    public function updateAccountsHead($request)
    {
        if (! $request instanceof AccountsHeadRequest) 
        {
            return back()->with('errmsg','Request must be an instance of AccountsHeadRequest class');
        }

        return $this->accountshead->editAccountsHead([
                    'id' => $request->account_rec_id,
                    'acc_head' => $request->acc_head,
                    'status' => $request->status,
                    'updated_at' => Auth::guard('root')->user()->id
        ]);
    }

    public function deleteAccountsHead($id)
    {
        return $this->accountshead->deleteAccountsHead($id);
    }
}
