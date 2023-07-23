<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserLoginRequest;
use App\Reseller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class SmsAppLoginController extends Controller
{
    use AuthenticatesUsers;
    
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * user login form
     *
     * @return void
     */
    public function showLoginForm()
    {
        return view('smsview.common.smsapplogin');
    }
    
    /**
     * manager login form
     *
     * @return void
     */
    public function showManagerLoginForm()
    {
        return view('smsview.common.smsappmanagerlogin');
    }
    
    /**
     * reseller login form
     *
     * @return void
     */
    public function showResellerLoginForm()
    {
        return view('smsview.common.smsappresellerlogin');
    }
    
    /**
     * root login form
     *
     * @return void
     */
    public function showRootLoginForm()
    {
        return view('smsview.common.smsapprootlogin');
    }
    

    public function clientPhoneVerifyForm()
    {
        return view('smsview.common.client-phone-verification');
    }

    public function verifyClientPhone(Request $request)
    {
        
        if (User::where('security_code', $request->security_code)->exists()) 
        {
            $user = User::where('security_code', $request->security_code)->first();
            Auth::guard('web')->login($user);
            $user->security_code = NULL;
            $user->phone_verified = true;
            $user->save();
            return redirect()->route('client');
        }

        return back()->with('errmsg','Invalid security code');
    }

    /**
     * multiple authentication user login
     *
     * @param Request $request
     * @return void
     */
    public function smsLogin(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $usertype = $request->usertype;

        // print_r($usertype);

        switch($usertype) {

            case 'manager':
                
                if (method_exists($this, 'hasTooManyLoginAttempts') &&
                    $this->hasTooManyLoginAttempts($request)) {
                    $this->fireLockoutEvent($request);

                    return back()->with('errmsg','Too many login attempts, You have been locked for 2 min');
                    return $this->sendLockoutResponse($request);
                }

                if (Auth::guard('manager')->attempt(['email' => $email, 'password' => $password])) {
                    
                    return redirect()->route('manager');

                }  

                $this->incrementLoginAttempts($request);

                if ($this->maxAttempts() <= 5) {
                    return back()->with('errmsg','Invalid User & Password');
                }
                

                return $this->sendFailedLoginResponse($request);
                
                break; 
            case 'reseller':
                if (method_exists($this, 'hasTooManyLoginAttempts') &&
                    $this->hasTooManyLoginAttempts($request)) {
                    $this->fireLockoutEvent($request);

                    return back()->with('errmsg','Too many login attempts, You have been locked for 2 min');
                    return $this->sendLockoutResponse($request);
                }

                $username = $this->username();

                if ($username == 'email') {

                    $reseller = Reseller::where('email', $request->identity)->first();
                } 

                if ($username == 'phone') {

                    $reseller = Reseller::where('phone', $request->identity)->first();
                }

                if ($reseller)
                {
                    

                    if ($username == 'email')
                    {
                        if (Auth::guard('reseller')->attempt(['email' => $request->identity, 'password' => $password])) {
                            
                            return redirect()->route('reseller');

                        }
                    } else {
                        if (Auth::guard('reseller')->attempt(['phone' => $request->identity, 'password' => $password])) {
                            
                            return redirect()->route('reseller');

                        }
                    }
                }

                /*if (Auth::guard('reseller')->attempt(['email' => $email, 'password' => $password])) {
                    
                    return redirect()->route('reseller');

                }*/    
                
                $this->incrementLoginAttempts($request);

                if ($this->maxAttempts() <= 5) {
                    return back()->with('errmsg','Invalid User & Password');
                }
                

                return $this->sendFailedLoginResponse($request);
                
                break; 
            case 'client':
                if (method_exists($this, 'hasTooManyLoginAttempts') &&
                    $this->hasTooManyLoginAttempts($request)) {
                    $this->fireLockoutEvent($request);

                    return back()->with('errmsg','Too many login attempts, You have been locked for 2 min');
                    return $this->sendLockoutResponse($request);
                }

                $username = $this->username();

                if ($username == 'email') {

                    $client = User::where('email', $request->identity)->first();
                } 

                if ($username == 'phone') {

                    $client = User::where('phone', $request->identity)->first();
                }

                if ($client)
                {
                    if ($username == 'email')
                    {
                        if (Auth::guard('web')->attempt(['email' => $request->identity, 'password' => $password])) {
                            
                            if (Auth::guard('web')->user()->status == 'n') {
                                return back()->with('errmsg','We have found unethical transection from your account, your account is blocked until the issue is solve.'); 
                            }
                            return redirect()->route('client');

                        }
                    } else {
                        if (Auth::guard('web')->attempt(['phone' => $request->identity, 'password' => $password])) {
                            
                            if (Auth::guard('web')->user()->status == 'n') {
                                return back()->with('errmsg','We have found unethical transection from your account, your account is blocked until the issue is solve.'); 
                            }

                            return redirect()->route('client');

                        }
                    }
                    /*if (!$client->phone_verified && $client->created_from == 'web site' && $client->created_by == 'web')
                    {
                        return redirect(route('verify-phone'));
                    } else {

                        if ($username == 'email')
                        {
                            if (Auth::guard('web')->attempt(['email' => $request->identity, 'password' => $password])) {
                                
                                return redirect()->route('client');

                            }
                        } else {
                            if (Auth::guard('web')->attempt(['phone' => $request->identity, 'password' => $password])) {
                                
                                return redirect()->route('client');

                            }
                        }
                    }*/
                }

                $this->incrementLoginAttempts($request);

                if ($this->maxAttempts() <= 5) {
                    return back()->with('errmsg','Invalid User & Password');
                }
                

                return $this->sendFailedLoginResponse($request);
                
                break; 
            default: 
                if (method_exists($this, 'hasTooManyLoginAttempts') &&
                    $this->hasTooManyLoginAttempts($request)) {
                    $this->fireLockoutEvent($request);

                    return back()->with('errmsg','Too many login attempts, You have been locked for 2 min');
                    return $this->sendLockoutResponse($request);
                }
                if (Auth::guard('root')->attempt(['email' => $email, 'password' => $password])) {

                    return redirect()->route('superadmin');
                    
                }    
                $this->incrementLoginAttempts($request);

                if ($this->maxAttempts() <= 5) {
                    return back()->with('errmsg','Invalid User & Password');
                }
                

                return $this->sendFailedLoginResponse($request);
                

        }
            
    }

    /**
    * Get the login username to be used by the controller.
    *
    * @return string
    */
    public function username()
    {
        $login = request()->input('identity');

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        request()->merge([$field => $login]);

        return $field;
    }

    /**
     * Get the number of minutes to throttle for.
     *
     * @return int
     */
    public function decayMinutes()
    {
        return property_exists($this, 'decayMinutes') ? $this->decayMinutes : 2;
    }
}
