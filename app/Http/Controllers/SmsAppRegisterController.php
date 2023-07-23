<?php

namespace App\Http\Controllers;

use Auth;
use Hash;
use App\User;
use App\Manager;
use App\Reseller;
use App\RootUser;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserFromRequest;
use App\Http\Resources\ManagerResource;
use App\Http\Resources\ResellerResource;
use App\Http\Resources\RootUserResource;

class SmsAppRegisterController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function logout()
    {
        //return $this->guard();
        if (Auth::guard('root')->check()) {

            Auth::guard('root')->logout();
            return redirect()->route('root-login');
        } else if (Auth::guard('manager')->check()) {

            Auth::guard('manager')->logout();
            return redirect()->route('manager-login');
        } else if (Auth::guard('reseller')->check()) {

            Auth::guard('reseller')->logout();
            return redirect()->route('reseller-login');
        } else if (Auth::guard('web')->check()) {

            Auth::guard('web')->logout();
            return redirect()->route('signin');
        } else {

            Auth::logout();
            return redirect()->route('signin');
        }
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
