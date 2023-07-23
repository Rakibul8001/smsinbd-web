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
                <h1 class="m-n font-thin h3 text-black font-bold">Support Manager Edit</h1>
                <small class="text-muted"></small>
            </div>
            <div class="col-sm-6 text-right hidden-xs">
                
            </div>
            </div>
        </div>
        <!-- / main header -->
        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
            <!-- stats -->

            <div class="row">
              
            <div class="col-md-12">
              <a class="btn btn-primary btn-addon btn-md pull-right mb-5" style="margin-bottom:10px; z-index: 9999; position:relative;" href="{{route('root-managers')}}"><i class="fa fa-angle-left"></i> Back to List</a>
                <div class="tab-container">
                    <ul class="nav nav-tabs">
                      <li class="active font-bold"><a href data-toggle="tab" data-target="#tab_10">GENERAL INFORMATION </a></li>
                      <!-- <li class="font-bold"><a href data-toggle="tab" data-target="#tab_2">DOCUMENTS UPLOAD </a></li>
                      <li class="font-bold"><a href data-toggle="tab" data-target="#tab_3">SETTINGS </a></li> -->
                    </ul>
                    <div class="tab-content">
                      <div class="tab-pane active" id="tab_10">
                        <div class="panel panel-default">
                            <div class="panel-heading font-bold text-right">General Information</div>
                            <div class="panel-body">
                                <div class="panel panel-default">
                                  <form role="form" action="{{route('root-user-update')}}" method="post">
                                  <div class="panel-heading font-bold">EDIT USER <span class="text-success" style="position: relative; left: 40%;">@if(session()->has('msg')) <span class="text-left">{{session()->get('msg')}}</span> @endif <span class="pull-right @if($user->status == '') text-danger @elseif($user->status == 'n') text-danger @elseif($user->status == 'y') text-success @endif">Published :@if($user->status == '') No @elseif($user->status == 'n') No @elseif($user->status == 'y') Published @endif</span></div>
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
                                                <input type="text" name="email" id="email" value="{{$user['email']}}" class="form-control {{$errors->has('email')?'border-danger':''}}" placeholder="Enter Email">
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
                                              <input type="text" name="phone" id="phone" value="{{$user['phone']}}" class="form-control {{$errors->has('phone')?'border-danger':''}}" placeholder="Enter Phone Number">
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
                                              <div class="col-md-3">
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
                                              <div class="col-md-3">
                                                <label class="font-bold">Status</label>
                                                <select class="form-control" name="status" id="status">
                                                    <option value="{{$user->status}}">@if($user->status == '') No @elseif($user->status == 'n') No @elseif($user->status == 'y') Yes @endif</option>
                                                    <option value="y">Yes</option>
                                                    <option value="n">No</option>
                                                </select>
                                              </div>
                                            </div>
                                          </div>
                      
                                          <input type="hidden" name="usertype" id="usertype" value="manager"/>
                                          <input type="hidden" name="paneltype" value="admin"/>
                                          <button type="submit" class="btn btn-primary btn-addon btn-md pull-right"><i class="fa fa-pencil"></i> UPDATE</button>
                                        
                                      </div>
                                    </form>
                                    </div>
                            </div>
                          </div>
                      </div>
                      <!-- <div class="tab-pane" id="tab_2">
                        <div class="panel panel-default">
                            <div class="panel-heading font-bold">THIS MONTH SALE GRAPH</div>
                            <div class="panel-body">
                              
                            </div>
                          </div>
                      </div> -->
                      <!-- <div class="tab-pane" id="tab_3">
                        <div class="panel panel-default">
                            <div class="panel-heading font-bold">THIS YEAR SALES GRAPH</div>
                            <div class="panel-body">
                              
                            </div>
                          </div>
                      </div> -->
                    </div>
                  </tabset>
                </div>
                
            </div>
            </div>
            <!-- / stats -->

        </div>
        </div>
        <!-- / main -->
        </div>



        </div>
        </div>
        <!-- /content -->
@endsection