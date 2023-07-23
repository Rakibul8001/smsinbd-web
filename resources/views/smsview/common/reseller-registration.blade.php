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
                  <h1 class="m-n font-thin h3 text-black font-bold">Reseller Registration</h1>
                  <small class="text-muted"></small>
              </div>
            </div>
        </div>
        <!-- / main header -->
          <div class="row m-t-md">
            <div class="col-md-9 col-md-offset-2">
              @if(session()->has('msg'))
                <div class="alert alert-success font-weight-bold clientsuccess" role="alert">
                  {{session()->get('msg')}}
                </div>
              @endif

              @if(session()->has('errmsg'))
                <div class="alert alert-danger font-weight-bold clientsuccess" role="alert">
                  {{session()->get('errmsg')}}
                </div>
              @endif
              <div class="panel panel-default">
              <div class="panel-heading font-bold">ADD RESELLER </div>
                <div class="panel-body">
                <form role="form" action="{{route('doregistration')}}" method="post">
                    @csrf
                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-6">
                          <label class="font-bold">Contact Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name')}}" class="form-control {{$errors->has('name') ? 'border-danger': ''}}" required placeholder="Enter Contact Name">
                          @error('name')
                          <span class="text-danger font-weight-bold">{{$errors->first('name')}}</span>
                          @enderror
                        </div>
                        <div class="col-md-6">
                          <label class="font-bold">Email <span class="text-danger">*</span></label>
                          <input type="email" name="email" id="email" value="{{ old('email')}}" class="form-control {{$errors->has('email')?'border-danger':''}}" required placeholder="Enter email">
                          @error('email')
                          <span class="text-danger font-weight-bold">{{$errors->first('email')}}</span>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-6">
                          <label class="font-bold">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="company" id="company" value="{{ old('company')}}" class="form-control {{$errors->has('company')?'border-danger':''}}" placeholder="Enter Company Name">
                        </div>
                        <div class="col-md-6">
                          <label class="font-bold">Contact Phone <span class="text-danger">*</span></label>
                          <input type="text" name="phone" id="phone" value="{{ old('phone')}}" class="form-control {{$errors->has('phone')?'border-danger':''}}" required placeholder="Enter Phone">
                          @error('phone')
                          <span class="text-danger font-weight-bold">{{$errors->first('phone')}}</span>
                          @enderror
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-6">
                          <label class="font-bold">Address  <span class="text-danger">*</span></label>
                          <textarea name="address" id="address" class="form-control" required placeholder="Enter your address">{{ old('address')}}</textarea>
                          @error('address')
                          <span class="text-danger font-weight-bold">{{$errors->first('address')}}</span>
                          @enderror
                        </div>
                        <div class="col-md-3">
                          <label class="font-bold">Password <span class="text-danger" style="font-weight:300;">*[min 6 char length]</span></label>
                          <input type="password" name="password" id="password" value="{{ old('password') }}" class="form-control {{$errors->has('password')?'border-danger':''}}" required placeholder="Enter Password">
                          @error('password')
                          <span class="text-danger font-weight-bold">{{$errors->first('password')}}</span>
                          @enderror
                        </div>
                        <div class="col-md-3">
                          <label class="font-bold">Confirm Password   <span class="text-danger" style="font-weight:300;">*</span></label>
                          <input type="password" name="password_confirmation" value="{{ old('password') }}" id="password_confirmation" class="form-control {{$errors->has('password_confirmation')?'border-danger':''}}" required placeholder="Confirm Password">
                          @error('password')
                            <span class="text-danger font-weight-bold">{{$errors->first('password')}}</span>
                          @enderror
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <div class="row">
                        <div class="col-md-4">
                          <label class="font-bold">Country <span class="text-danger">*</span></label>
                        <select ui-jq="chosen" class="w-full clientregistration {{$errors->has('country')?'border-danger':''}}" name="country" id="country">
                            
                            <optgroup label="All Countries">
                                <option value=""{{old('country')}}>{{old('country')}}</option>
                                @foreach ($countries as $country)

                                  <option value="{{$country->country_name}}" @if($country->default_country == true) selected @else @endif>{{$country->country_name}}</option>
                                    
                                @endforeach
                            </optgroup>
                            
                        </select>
                          @error('country')
                          <span class="text-danger font-weight-bold">{{$errors->first('country')}}</span>
                          @enderror
                        </div>
                        <div class="col-md-3">
                          <label class="font-bold">City  <span class="text-danger">*</span></label>
                          <input type="text" name="city" id="city" class="form-control" value="{{old('city')}}" required placeholder="Enter your city">
                          @error('city')
                          <span class="text-danger font-weight-bold">{{$errors->first('city')}}</span>
                          @enderror
                        </div>
                        <div class="col-md-3">
                          <label class="font-bold">State</label>
                          <input type="text" name="state" id="state" class="form-control" value="{{old('state')}}" placeholder="Enter your state">
                        </div>
                        <div class="col-md-2" style="margin-top: 25px;">
                            <button type="submit" class="btn btn-primary btn-addon btn-md pull-right"><i class="fa fa-save"></i> Submit</button>
                        </div>
                        <input type="hidden" name="usertype" id="usertype" value="reseller"/>
                        
                      </div>
                    </div>


                    <input type="hidden" name="paneltype" value="admin panel"/>
                    
                  </form>
                </div>
              </div>
            </div>
        </div>
        
        </div>
        <!-- / main -->
        <!-- right col -->
        
        <!-- / right col -->
        </div>



        </div>
        </div>
        <!-- /content -->
@endsection