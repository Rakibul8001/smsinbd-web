@extends('layouts.smsapp')

@section('appbody')
    <!-- content -->
  <div id="content" class="app-content" role="main">
    <div class="app-content-body ">
      

        <div class="hbox hbox-auto-xs hbox-auto-sm" ng-init="
        app.settings.asideFolded = false; 
        app.settings.asideDock = false;
        ">
        <!-- main -->
        <div class="col">
        <!-- main header -->
        <div class="bg-light lter b-b wrapper-md">
            <div class="row">
            <div class="col-sm-6 col-xs-12">
                @include('smsview.common.user-head-title')
                <small class="text-muted">Welcome to SMSBD Application</small>
            </div>
            <div class="col-sm-6 col-xs-12 text-right hidden-xs">
                <h1 class="m-n font-thin h3 text-black font-bold">Client Profile</h1>
                <small class="text-muted"></small>
            </div>
            <div class="col-sm-6 text-right hidden-xs">
                
            </div>
            </div>
        </div>
        <!-- / main header -->
        <div>
            <div class="wrapper-lg bg-white-opacity">
                <div class="row m-t">
                <div class="col-sm-7">
                    <a href class="thumb-lg pull-left m-r" style="border: 1px solid #ddd;
                                    border-radius: 100%;
                                    height: 70px;
                                    width: 70px;
                                    text-align: center;
                                    line-height: 67px;
                                    font-size: 24px;
                                    background-color: #ddd;">
                    <i class="icon-user"></i>
                    </a>
                    <div class="clear m-b">
                    <div class="m-b m-t-sm">
                        <span class="h3 text-black">{{$user->name}}</span>
                        <small class="m-l">{{$user->address}}</small>
                    </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="pull-right pull-none-xs text-center">
                    <a href class="m-b-md inline m">
                        <span class="h3 block font-bold">Mask</span>
                        <small>{{$totalmaskbal}}</small>
                    </a>
                    <a href class="m-b-md inline m">
                        <span class="h3 block font-bold">Non Mask</span>
                        <small>{{$totalnonmaskbal}}</small>
                    </a>
                    <a href class="m-b-md inline m">
                        <span class="h3 block font-bold">Voice</span>
                        <small>{{$totalvoicebal}}</small>
                    </a>
                    </div>
                </div>
                </div>
            </div>
            </div>
            <div class="wrapper bg-white b-b">
            @if(Auth::guard('reseller')->check())  
              @include('smsview.rootadmin.profiletab-reseller-client')
            @else 
              @include('smsview.rootadmin.profiletab')
            @endif
            </div>
            <div class="padder" style="margin-top: 20px;">     
                @if(session()->has('msg'))
                    <div class="alert alert-success font-weight-bold clientsuccess" role="alert">
                    {{session()->get('msg')}}
                    </div>
                @endif  
                <div class="alert alert-danger font-weight-bold clientunsuccess" style="display:none;" role="alert"></div>
                <div class="panel panel-default">
                    <form role="form" action="{{route('root-user-update')}}" method="post">
                    <div class="panel-heading font-bold">General Information <span  data-verified="{{@$user->documents[0]->isVerified}}" class="pull-right doverify @if(@$user->documents[0]->isVerified == false) text-danger @elseif(@$user->documents[0]->isVerified == true) text-success @endif">Verified :@if(@$user->documents[0]->isVerified == true) Yes @elseif(@$user->documents[0]->isVerified == false) No @endif</span></div>
                    <div class="panel-body">
                        <div class="panel panel-default">
                            
                        <div class="panel-heading font-bold">EDIT USER <span data-status="{{$user->status}}" class="pull-right dostatus @if($user->status == '') text-danger @elseif($user->status == 'n') text-danger @elseif($user->status == 'y') text-success @endif">Status :@if($user->status == '') No @elseif($user->status == 'n') No @elseif($user->status == 'y') Yes @endif</span></div>
                                <div class="panel-body">
                                
                                    @csrf
                                    <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                        <label class="font-bold">Contact Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" value="{{$user['name']}}" class="form-control {{$errors->has('name') ? 'border-danger': ''}}" required placeholder="Enter Contact Name">
                                        </div>
                                        <div class="col-md-6">
                                        <label class="font-bold">Email <span class="text-danger">*</span></label>
                                        <input type="text" name="email" id="email" value="{{$user['email']}}" class="form-control {{$errors->has('email') ? 'border-danger': ''}}" required placeholder="Enter Email">
                                        <!-- <div class="form-control">user['phone']</div> -->
                                        </div>
                                    </div>
                                    </div>
                                    <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                        <label class="font-bold">Company Name <span class="text-danger">*</span></label>
                                        <input type="text" name="company" id="company" value="{{$user['company']}}" class="form-control {{$errors->has('company')?'border-danger':''}}" placeholder="Enter Company Name">
                                        </div>
                                        <div class="col-md-6">
                                        <label class="font-bold">Contact Phone <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" id="phone" value="{{$user['phone']}}" class="form-control {{$errors->has('phone') ? 'border-danger': ''}}" required placeholder="Enter Phone">
                                        <!-- <div class="form-control">$user['phone']</div> -->
                                        </div>
                                    </div>
                                    </div>
                
                                    <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                        <label class="font-bold">Address</label>
                                        <textarea name="address" id="address" class="form-control" placeholder="Enter your address">{{$user['address']}}</textarea>
                                        </div>
                                        <div class="col-md-3">
                                        <label class="font-bold">Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password" id="password" class="form-control {{$errors->has('password')?'border-danger':''}}" placeholder="Enter Password">
                                        </div>
                                        <div class="col-md-3">
                                        <label class="font-bold">Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control {{$errors->has('password_confirmation')?'border-danger':''}}" placeholder="Enter Password">
                                        </div>
                                    </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2">
                                            <label class="font-bold">Country <span class="text-danger">*</span></label>
                                            <select class="form-control w-full clientregistration {{$errors->has('country')?'border-danger':''}}" name="country" id="country">
                                                
                                                
                                                    <option selected value="{{$user->country}}">{{$user->country}}</option>
                                                    @foreach ($countries as $country)
                    
                                                    <option value="{{$country->country_name}}">{{$country->country_name}}</option>
                                                        
                                                    @endforeach
                                                
                                            </select>
                                            </div>
                                            <div class="col-md-3">
                                            <input type="hidden" name="id" value="{{$user->id}}"/>
                                            <label class="font-bold">City </label>
                                            <input type="text" name="city" id="city" class="form-control" value="{{$user->city}}" required placeholder="Enter your city">
                                            </div>
                                            <div class="col-md-3">
                                            <label class="font-bold">State</label>
                                            <input type="text" name="state" id="state" class="form-control" value="{{$user->state}}" required placeholder="Enter your state">
                                            </div>
                                            <div class="col-md-2">
                                            <label class="font-bold">Status</label>
                                            <select class="form-control" name="status" id="status">
                                                <option value="{{$user->status}}">@if($user->status == '') No @elseif($user->status == 'n') No @elseif($user->status == 'y') Yes @endif</option>
                                                <option value="y">Yes</option>
                                                <option value="n">No</option>
                                            </select>
                                            </div>
                                            <div class="col-md-2" style="margin-top: 20px;">
                                            <label class="checkbox-inline">
                                                <input type="checkbox"name="live_dipping" id="live_dipping" @if($user->live_dipping == true) checked @else($user->live_dipping == false) unchecked @endif value="{{ ($user->live_dipping=='1')? 'y' : 'n' }}"> Dipping
                                            </label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <br>
                                            <div class="col-md-6">
                                                <label class="checkbox-inline">
                                                    <input type="checkbox"name="otp_allowed" id="otp_allowed" @if($user->otp_allowed == 1) checked @else($user->otp_allowed == 0) unchecked @endif value="{{ ($user->otp_allowed=='1')? 'y' : 'n' }}"> OTP Allowed for the client
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <input type="hidden" name="usertype" id="usertype" value="client"/>
                                    <input type="hidden" name="paneltype" value="admin"/>
                                    <button type="submit" class="btn btn-primary btn-addon btn-md pull-right"><i class="fa fa-pencil"></i> UPDATE</button>
                                
                                </div>
                            
                            </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- / main -->
        </div>



        </div>
        </div>
        <!-- /content -->
        
@endsection