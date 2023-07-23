<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\RootUser;
use App\Reseller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class PasswordResetController extends Controller
{ 
    use AuthenticatesUsers;
    public function passwordResetForm()
    {
        $host = request()->getHttpHost();
        $url = request()->headers->get('referer');
        $refererAction = Str::after($url, $host."/");

        if ($refererAction == 'reseller-login') {
            $requestfor = 'passwordreset';
            $passwordResetFor = 'reseller';
            return view('smsview.common.password-reset-form',compact('passwordResetFor','requestfor'));
        } else {
            $requestfor = 'passwordreset';
            $passwordResetFor = 'user';
            return view('smsview.common.password-reset-form-client',compact('passwordResetFor','requestfor'));
        }
    }


    public function resetNewPassword()
    {
        
        return view('smsview.common.reset-new-password-form');
    }

    public function updateUserPassword(Request $request)
    {
        $passwordresetfor = session()->get('passwordresetfor');

        $userphone = session()->get('userphone');

        if ($passwordresetfor == "user") {

            $user = User::where('phone', session()->get('userphone'))->first();
            $user->password = Hash::make($request->resetpassword);
            $user->security_code = NULL;
            $user->save();

            Auth::guard('web')->login($user);

            return redirect()->route('client');

        } else if ($passwordresetfor == "reseller") {

            $user = Reseller::where('phone', session()->get('userphone'))->first();
            $user->password = Hash::make($request->resetpassword);
            $user->security_code = NULL;
            $user->save();

            Auth::guard('reseller')->login($user);

            return redirect()->route('reseller');

        } else if ($passwordresetfor == "root") {

            $user = RootUser::where('phone', session()->get('userphone'))->first();
            $user->password = Hash::make($request->resetpassword);
            $user->security_code = NULL;
            $user->save();

            Auth::guard('root')->login($user);

            return redirect()->route('superadmin');

        }

        return response()->json(['errmsg' => 'Usertype Not Defined'], 406);
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

    public function sendPasswordInformation(Request $request)
    {
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return back()->with('errmsg','Too many attempts, You have been locked for 2 min');
            return $this->sendLockoutResponse($request);
        }

        $username = $this->username();
        // echo $username; exit;
        $code = mt_rand(1000,9999);


        if ($request->passwordResetFor == "user") {

            if ($username == 'email') {

                $user = User::where('email', $request->identity)->first();
            
            } 

            if ($username == 'phone') {

                $user = User::where('phone', $request->identity)->first();
            }
        } else if ($request->passwordResetFor == "reseller") {

            if ($username == 'email') {

                $user = Reseller::where('email', $request->identity)->first();
            
            } 

            if ($username == 'phone') {

                $user = Reseller::where('phone', $request->identity)->first();
            }
        } else if ($request->passwordResetFor == "root") {

            if ($username == 'email') {

                $user = RootUser::where('email', $request->identity)->first();
            
            } 

            if ($username == 'phone') {

                $user = RootUser::where('phone', $request->identity)->first();
            }
        }

        if ($user) {

            $code = mt_rand(1000,9999);
            $user->security_code = $code;
            $user->save();


            if ($user instanceof \Illuminate\Contracts\Auth\Authenticatable) {
              
                request()->merge(['user' => $user, 'requestfor' => $request->requestfor]);

                redirect(route('root-client-phone-verified-sms-send',['user' => $user,'phone' => $user->phone,'requestfor' => $request->requestfor,'passwordresetfor' => $request->passwordResetFor]));

                return redirect(route('verify-phone'));
            }
        }

        $this->incrementLoginAttempts($request);

        if ($this->maxAttempts() <= 2) {
            return back()->with('errmsg','Invalid User & Password');
        }
        
        return back();
        //return $this->sendFailedLoginResponse($request);
    }
}