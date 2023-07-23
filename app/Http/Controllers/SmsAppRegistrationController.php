<?php

namespace App\Http\Controllers;

use Auth;
use Hash;
use App\User;
use App\Manager;
use App\Reseller;
use App\RootUser;
use App\UserDocument;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Core\Users\ClientInterface;
use App\Core\Users\ManagerInterface;
use App\Http\Resources\UserResource;
use App\Core\Users\ResellerInterface;
use App\Core\Users\RootUserInterface;
use App\Http\Requests\UserFromRequest;
use App\Http\Resources\ManagerResource;
use App\Http\Resources\ResellerResource;
use App\Http\Resources\RootUserResource;
use App\Core\Countries\CountriesInterface;
use App\Http\Requests\UserEditFormRequest;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class SmsAppRegistrationController extends Controller
{
    /**
     * RootUser Service
     *
     * @var Object App\Core\Users\RootUserRepository
     */
    protected $rootuser;

    /**
     * Manager Serive
     *
     * @var Object App\Core\Users\ManagerRepository
     */
    protected $manager;

    /**
     * Reseller Service
     *
     * @var Object App\Core\Users\ResellerRepository
     */
    protected $reseller;

    /**
     * Client Serive
     *
     * @var Ojbect App\Core\Users\ClientRepository
     */
    protected $client;

    /**
     * country Serive
     *
     * @var Object App\Core\Countries\Countries
     */
    protected $country;

    public function __construct(RootUserInterface $rootuser, 
                                ManagerInterface $manager,
                                ResellerInterface $reseller,
                                ClientInterface $client,
                                CountriesInterface $country
    )
    {
        $this->rootuser = $rootuser;

        $this->manager = $manager;

        $this->reseller = $reseller;

        $this->client = $client;

        $this->country = $country;
    }

    public function checkClientUsingFirebase(Request $request)
    {
        $number1 = '';
        $number2 = '';
        if(strlen($request->phone) >= 11  && is_numeric($request->phone)){  
            $checknumb= substr($request->phone, 0, 2) ;
            if($checknumb=='88'){
                $number1= trim(substr($request->phone, 2, 11)) ;  
            }
            $number2= trim($request->phone) ;
        }
        if(User::where('phone',$number1)->orWhere('phone',$number2)->exists()) {
            $clientcheck = User::where('phone',$number1)->orWhere('phone',$number2)->first();
            return response()->json(['errmsg' => 'User already exists'],200);
            //return back()->with('errmsg','Client already exists');
        }
    }

    public function checkClient(Request $request) {

        if(
            User::where('phone',Str::substr($request->phone,2,Str::length($request->phone)))->exists() || 
            User::where('phone',Str::substr($request->phone,0,Str::length($request->phone)))->exists()) 
        {
            return redirect()->route('user-registration')->with('errmsg','Client already exists');;
        }

        $checkfrbreg = DB::table('firebase_registration')
                            ->where('phone',Str::substr($request->phone,2,Str::length($request->phone)))
                            ->orWhere('phone',Str::substr($request->phone,0,Str::length($request->phone)))
                            ->get();

        if ($checkfrbreg->isEmpty()) {                    
            DB::table('firebase_registration')->insert([
                'phone' => $request->phone,
                'uid' => $request->uid,
                'acctoken' => $request->token,
                'regdate' => Carbon::today()
            ]);
            session()->put('firebasephone',$request->phone);
        }

        return redirect()->route('user-registration');
    }

    /**
     * Add user.
     *
     * @param  array  $data
     * @return \App\User
     */
    public function register(Request $request)
    {

        $host = $request->getHttpHost();
        $url = $request->headers->get('referer');
        $refererAction = Str::after($url, $host."/");
        $code = mt_rand(1000,9999);
        
        if ($request->usertype == 'root') {
            
            if($refererAction == 'user-registration') {
                return back()->with('errmsg','Unauthorize action not allowed');
            }

            if(RootUser::where('email',$request->email)->orWhere('phone',$request->phone)->exists()) {
                $rootcheck = RootUser::where('email',$request->email)->orWhere('phone',$request->phone)->first();
                return back()->with('errmsg','Root already exists');
            }

            $user = $this->rootuser->addRoot([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'company' => $request->company,
                'phone' => $request->phone,
                'address' => $request->address,
                'country' => $request->country,
                'city' => $request->city,
                'state' => $request->city,
                'created_from' => 'super admin',
                'created_by' => 'root',
                'status' => 'y',
            ]);

            if ($user instanceof \Illuminate\Contracts\Auth\Authenticatable) {
                Auth::guard('root')->login($user);
                return back()->with('msg','Root user successfully created');
            }

            return back();

        } else if($request->usertype == 'manager') {

            if($refererAction == 'user-registration') {
                return back()->with('errmsg','Unauthorize action not allowed');
            }
            
            if(Manager::where('email',$request->email)->orWhere('phone',$request->phone)->exists()) {
                $managercheck = Manager::where('email',$request->email)->orWhere('phone',$request->phone)->first();
                return back()->with('errmsg','Manager already exists');
            }

            $user = $this->manager->addManager([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'company' => $request->company,
                'phone' => $request->phone,
                'address' => $request->address,
                'country' => $request->country,
                'city' => $request->city,
                'state' => $request->city,
                'root_user_id' => Auth::guard('root')->user()->id,
                'created_from' => 'super admin',
                'created_by' => 'root',
                'status' => 'y',
            ]);

            return back()->with('msg','Manager successfully created');
            
            
        } else if($request->usertype == 'reseller') {

            if($refererAction == 'user-registration') {
                return back()->with('errmsg','Unauthorize action not allowed');
            }
            
            if(Reseller::where('email',$request->email)->orWhere('phone',$request->phone)->exists()) {
                $resellercheck = Reseller::where('email',$request->email)->orWhere('phone',$request->phone)->first();
                return back()->with('errmsg','Reseller already exists');
            }

            /**
             * check authenticate admin and admin type
             */
            if (Auth::guard('root')->check()) {

                $rootid = Auth::guard('root')->user()->id;

                $created_from = 'root panel';

                $created_by = 'root';

            } else if (Auth::guard('manager')->check()) {

                $managerid = Auth::guard('manager')->user()->id;

                $created_from = 'manager panel';

                $created_by = 'manager';

            } 

            $user = $this->reseller->addReseller([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'company' => $request->company,
                'phone' => $request->phone,
                'address' => $request->address,
                'country' => $request->country,
                'city' => $request->city,
                'state' => $request->city,
                'root_user_id' => 1,
                'manager_id' => !empty($managerid) ? $managerid : 1,
                'created_from' => $created_from,
                'created_by' => $created_by,
                'status' => 'y',
                'verified' => 'n'
            ]);

            if (Auth::guard('manager')->check()) {

                $manager = Auth::guard('manager')->user()->name;
                DB::table("staff_activities")
                    ->insert([
                        'manager_id' => Auth::guard('manager')->user()->id,
                        'activity_name' => 'Add New Reseller',
                        'activity_type' => 'Insert',
                        'activity_desc' => "Manager {$manager} add new reseller {$user->name}/{$user->email}/{$user->phone}",
                        'record_id' => $user->id,
                        'invoice_val' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
            }

            return back()->with('msg','Reseller successfully created');

        } else if($request->usertype == 'client') {
            
            if(User::where('email',$request->email)->orWhere('phone',$request->phone)->exists()) {
                $clientcheck = User::where('email',$request->email)->orWhere('phone',$request->phone)->first();
                return back()->with('errmsg','Client already exists');
            }


            /**
             * check authenticate admin and admin type
             */
            if (Auth::guard('root')->check()) {

                $rootid = Auth::guard('root')->user()->id;

                $created_from = 'root panel';

                $created_by = 'root';

            } else if (Auth::guard('manager')->check()) {

                $managerid = Auth::guard('manager')->user()->id;

                $created_from = 'manager panel';

                $created_by = 'manager';

            } else if (Auth::guard('reseller')->check()) {

                $resellerid = Auth::guard('reseller')->user()->id;

                $created_from = 'reseller panel';

                $created_by = 'reseller';

            } else if (Auth::guard('web')->check()) {

                $clientid = Auth::guard('web')->user()->id;

                $created_from = 'web site';

                $created_by = 'web';

            }  else {


                $created_from = 'web site';

                $created_by = 'web';

            }

            if ($created_by == 'web') {
                $checkfrbreg = DB::table('firebase_registration')
                            ->where('phone',Str::substr($request->phone,2,Str::length($request->phone)))
                            ->orWhere('phone',Str::substr($request->phone,0,Str::length($request->phone)))
                            ->get();

                if ($checkfrbreg->isEmpty()) {
                    return redirect()->route('verify-your-phone');
                }
            }

            $user = $this->client->addClient([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'company' => $request->company,
                'phone' => $request->phone,
                'address' => $request->address,
                'country' => $request->country,
                'city' => $request->city,
                'state' => $request->city,
                'root_user_id' => !empty($rootid) ? $rootid : 1,
                'manager_id' => !empty($managerid) ? $managerid : 1,
                'reseller_id' => !empty($resellerid) ? $resellerid : 0,
                'created_from' => $created_from,
                'created_by' => $created_by,
                'status' => 'y',
                'verified' => 'n',
                'security_code' => $code,
                'api_token' => Str::random(40),
                'phone_verified' => false
            ]);
            
            //nid
            if(isset($request->nationalid)) {
                $filename = date("YmdHis").$user->id.".".$request->nationalid->getClientOriginalExtension();

                $path = "nid/";
    
                $img = $request->nationalid->move($path, $filename );
    
                $this->nationalid = $filename;
    
    
                $documents = UserDocument::create([
                        'user_id' => $user->id,
                        'nid' => $this->nationalid,
                        'root_user_id' => 1,
                        'manager_id' => 0, 
                        'reseller_id' => 0,
                        'user_type' => $request->usertype,
                    ]);
            }
            
            //nid end

            
            if (Auth::guard('root')->check()) {

                return back()->with('msg','Client successfully created');

            } else if(Auth::guard('manager')->check()) {

                $manager = Auth::guard('manager')->user()->name;
                DB::table("staff_activities")
                    ->insert([
                        'manager_id' => Auth::guard('manager')->user()->id,
                        'activity_name' => 'Add New Client',
                        'activity_type' => 'Insert',
                        'activity_desc' => "Manager {$manager} add new client {$user->name}/{$user->email}/{$user->phone}",
                        'record_id' => $user->id,
                        'invoice_val' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                return back()->with('msg','Client successfully created');

            } else if(Auth::guard('reseller')->check()) {

                return back()->with('msg','Client successfully created');

            } else {

                if ($request->paneltype == "admin")
                {
                    if($refererAction == 'user-registration') {
                        return back()->with('errmsg','Unauthorize action not allowed');
                    }
                    return back()->with('msg','Client successfully created');
                }

                if ($user instanceof \Illuminate\Contracts\Auth\Authenticatable) {
                    //if ($user->phone_verified == true)
                    //{
                        Auth::guard('web')->login($user);
                        return redirect()->route('client');
                    //}

                    //request()->merge(['user' => $user]);

                    //return redirect(route('root-client-phone-verified-sms-send',['user' => $user,'phone' => $user->phone]));

                    //return redirect(route('verify-phone'));
                }
            }
        }
    }

    public function generateNewApiToken($user)
    {
        $api_token = Str::random(40);
        if (Auth::guard('web')->check())
        {
            $user = User::where('id',$user)->first();
            $user->api_token = $api_token;
            $user->save();
        }

        return back()->with('apitokenmsg', 'Token successfully generated');
    }


    /**
     * Edit user.
     *
     * @param  array  $data
     * @return \App\User
     */
    public function rootUserUpdate(Request $request)
    {
        
        $host = $request->getHttpHost();
        $url = $request->headers->get('referer');
        $refererAction = Str::after($url, $host."/");

        if ($request->usertype == 'root') {
            
            if($refererAction == 'user-registration') {
                return back()->with('errmsg','Unauthorize action not allowed');
            }
            
            if(!empty($request->password)) {
                
                $user = $this->rootuser->rootUserUpdate([
                    'id' => $request->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => $request->password,
                    'company' => $request->company,
                    'address' => $request->address,
                    'country' => $request->country,
                    'city' => $request->city,
                    'state' => $request->city,
                    'status' => $request->status,
                ]);

                return back()->with('msg','Root user successfully updated');
            } else {
                $user = $this->rootuser->rootUserUpdate([
                    'id' => $request->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'company' => $request->company,
                    'address' => $request->address,
                    'country' => $request->country,
                    'city' => $request->city,
                    'state' => $request->city,
                    'status' => $request->status,
                ]);

                return back()->with('msg','Root user successfully updated');
            }

            if ($user instanceof \Illuminate\Contracts\Auth\Authenticatable) {
                Auth::guard('root')->login($user);
                return back()->with('msg','Root user successfully updated');
            }

            return back();

        } else if($request->usertype == 'manager') {

            if($refererAction == 'user-registration') {
                return back()->with('errmsg','Unauthorize action not allowed');
            }

            if(!empty($request->password)) {
                
                $user = $this->manager->managerUpdate([
                    'id' => $request->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => $request->password,
                    'company' => $request->company,
                    'address' => $request->address,
                    'country' => $request->country,
                    'city' => $request->city,
                    'state' => $request->city,
                    'status' => $request->status,
                ]);
            } else {
                $user = $this->manager->managerUpdate([
                    'id' => $request->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'company' => $request->company,
                    'address' => $request->address,
                    'country' => $request->country,
                    'city' => $request->city,
                    'state' => $request->city,
                    'status' => $request->status,
                ]);
            }

            return back()->with('msg','Manager successfully updated');
            
            
        } else if($request->usertype == 'reseller') {

            if($refererAction == 'user-registration') {
                return back()->with('errmsg','Unauthorize action not allowed');
            }

            /**
             * check authenticate admin and admin type
             */
            if (Auth::guard('root')->check()) {

                $rootid = Auth::guard('root')->user()->id;

            } else if (Auth::guard('manager')->check()) {

                $managerid = Auth::guard('manager')->user()->id;

            } 

            if(!empty($request->password)) {
                
                $user = $this->reseller->resellerUserUpdate([
                    'id' => $request->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => $request->password,
                    'company' => $request->company,
                    'address' => $request->address,
                    'country' => $request->country,
                    'city' => $request->city,
                    'state' => $request->city,
                    'status' => $request->status,
                ]);
            } else {
                $user = $this->reseller->resellerUserUpdate([
                    'id' => $request->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'company' => $request->company,
                    'address' => $request->address,
                    'country' => $request->country,
                    'city' => $request->city,
                    'state' => $request->city,
                    'status' => $request->status,
                ]);
            }

            if (Auth::guard('manager')->check()) {
                
                $manager = Auth::guard('manager')->user()->name;

                $edituser = Reseller::where('id', $request->id)->first();

                DB::table("staff_activities")
                    ->insert([
                        'manager_id' => Auth::guard('manager')->user()->id,
                        'activity_name' => 'Update Reseller',
                        'activity_type' => 'Update',
                        'activity_desc' => "Manager {$manager} update reseller {$edituser->name}/{$edituser->email}/{$edituser->phone}",
                        'record_id' => $request->id,
                        'invoice_val' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

            }

            return back()->with('msg','Reseller successfully updated');

        } else if($request->usertype == 'client') {

            /**
             * check authenticate admin and admin type
             */
            if (Auth::guard('root')->check()) {

                $rootid = Auth::guard('root')->user()->id;

            } else if (Auth::guard('manager')->check()) {

                $managerid = Auth::guard('manager')->user()->id;

            } else if (Auth::guard('reseller')->check()) {

                $resellerid = Auth::guard('reseller')->user()->id;

            } else if (Auth::guard('web')->check()) {

                $clientid = Auth::guard('web')->user()->id;

            } 

            //$api_token = Str::random(40);


            if(!empty($request->password)) {
                
                
                $user = $this->client->clientUpdate([
                    'id' => $request->id,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'name' => $request->name,
                    'password' => $request->password,
                    'company' => $request->company,
                    'address' => $request->address,
                    'country' => $request->country,
                    'city' => $request->city,
                    'state' => $request->city,
                    'live_dipping' => ($request->live_dipping=='y')? 1 : 0, 
                    'otp_allowed' => ($request->otp_allowed=='y')? 1 : 0,
                    'status' => $request->status,
                    //'api_token' => $api_token
                ]);
            } else {
                $user = $this->client->clientUpdate([
                    'id' => $request->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'company' => $request->company,
                    'address' => $request->address,
                    'country' => $request->country,
                    'city' => $request->city,
                    'state' => $request->city,
                    'live_dipping' => ($request->live_dipping=='y')? 1 : 0, 
                    'otp_allowed' => ($request->otp_allowed=='y')? 1 : 0,
                    'status' => $request->status,
                    //'api_token' => $api_token
                ]);
            }



            
            if (Auth::guard('root')->check()) {

                return back()->with('msg','Client successfully updated');

            } else if(Auth::guard('manager')->check()) {

                $manager = Auth::guard('manager')->user()->name;

                $edituser = User::where('id', $request->id)->first();

                DB::table("staff_activities")
                    ->insert([
                        'manager_id' => Auth::guard('manager')->user()->id,
                        'activity_name' => 'Update Client',
                        'activity_type' => 'Update',
                        'activity_desc' => "Manager {$manager} update client {$edituser->name}/{$edituser->email}/{$edituser->phone}",
                        'record_id' => $request->id,
                        'invoice_val' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                return back()->with('msg','Client successfully updated');

            } else if(Auth::guard('reseller')->check()) {

                return back()->with('msg','Client successfully updated');

            } else {

                if ($request->paneltype == "admin")
                {
                    if($refererAction == 'user-registration') {
                        return back()->with('errmsg','Unauthorize action not allowed');
                    }
                    return back()->with('msg','Client successfully updated');
                }

                if ($user instanceof \Illuminate\Contracts\Auth\Authenticatable) {
                    
                    return back()->with('msg','Client successfully updated');
                }
            }
        }
    }



    public function smsappUserRegister()
    {
        $countries = $this->country->show();

        //return view('smsview.common.user-registration-firebase');
        return view('smsview.common.user-register_backup',compact('countries'));
    }

}
