<!DOCTYPE html>
<html lang="en" class="">
<head>
  <meta charset="utf-8" />
  <title>SMS Portal | SMS Service Provider</title>
  <meta name="description" content="app, web app, responsive, responsive layout, admin, admin panel, admin dashboard, flat, flat ui, ui kit, AngularJS, ui route, charts, widgets, components" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <link rel="stylesheet" href="{{ asset('libs/assets/animate.css/animate.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('libs/assets/font-awesome/css/font-awesome.min.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('libs/assets/simple-line-icons/css/simple-line-icons.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('libs/jquery/bootstrap/dist/css/bootstrap.css') }}" type="text/css" />

  <link rel="stylesheet" href="{{ asset('smsapp/css/font.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('smsapp/css/app.css') }}" type="text/css" />
  <style type="text/css">
    .thumb-sm {
        display: inline-block;
        width: 70px;
    }
    label {
      margin-top: 3px;
    }
  </style>

</head>
<body style="background-color:#222;">
<div class="app app-header-fixed ">
  

<div class="container w-xxl w-auto-xs">
    

  
 
</div>

<div class="container">
     <!-- content -->
   <div class="row">
       <div class="col-md-2">
        <img src="{{ asset('images/logo.png')}}" style="left:46%; right:0; position:relative; margin-left:auto; margin-right:auto;"/>
        <a href class="navbar-brand block m-t">DataHostIT</a>
        <div class="m-b-lg">
          <div class="wrapper text-center">
            <strong>Sign in to get in touch</strong>
          </div>
      
        <a href="{{route('signin')}}" class="btn btn-lg btn-primary btn-block">Log in</a>
        </div>
       </div>
       <div class="col-md-10">
        <div class="col-md-12">
            <div class="panel panel-default">
            <div class="panel-heading font-bold">CLIENT SIGN UP
              @if(session()->has('errmsg'))
                <span class="pull-right text-danger">{{session()->get('errmsg')}}</span> 
              @endif
            </div>
              <div class="panel-body">
              <form role="form" action="{{route('doregistration')}}" method="post" enctype="multipart/form-data">
                  @csrf
                  <div class="form-group">
                    <div class="row">
                      <div class="col-md-12">
                        <label class="font-bold">Contact Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{old('name')}}" required placeholder="Enter Contact Name">
                        @error('name')
                        <span class="text-danger font-weight-bold">{{$errors->first('name')}}</span>
                        @enderror
                      </div>
                      <div class="col-md-12">
                        <label class="font-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" value="{{old('email')}}" class="form-control" required placeholder="Enter email">
                        @error('email')
                        <span class="text-danger font-weight-bold">{{$errors->first('email')}}</span>
                        @enderror
                      </div>
                      <div class="col-md-12">
                        <label class="font-bold">Organization Name </label>
                        <input type="text" name="company" id="company" value="{{old('company')}}" class="form-control" placeholder="Enter Company Name">
                        
                      </div>
                      <div class="col-md-12">
                        <label class="font-bold">Address <span class="text-danger">*</span></label>
                        <textarea name="address" id="address" class="form-control" required placeholder="Enter your address">{{old('address')}}</textarea>
                        @error('address')
                        <span class="text-danger font-weight-bold">{{$errors->first('address')}}</span>
                        @enderror
                      </div>

                      <div class="col-md-12">
                        <label class="font-bold">Country <span class="text-danger">*</span></label>
                        <select class="form-control" ui-jq="chosen" class="w-full clientregistration" name="country" id="country">
                          <option  value="{{old('country')}}">{{old('country')}}</option>  
                          <optgroup label="All Countries">
                              @foreach ($countries as $country)
                              
                                <option value="{{$country->country_name}}" @if($country->default_country == true) selected @else  @endif>{{$country->country_name}}</option>
                                  
                              @endforeach
                          </optgroup>
                          
                        </select>
                        @error('country')
                        <span class="text-danger font-weight-bold">{{$errors->first('country')}}</span>
                        @enderror
                      </div>

                      <div class="col-md-12">
                        <label class="font-bold">City</label>
                        <input type="text" name="city" id="city" value="{{old('city')}}" class="form-control" required placeholder="Enter your city">
                      </div>
                    </div>
                  </div>
                  <input type="hidden" name="phone" id="phone" value="{{old('phone') ? old('phone') : session()->get('firebasephone')}}" class="form-control" placeholder="Enter Phone">
                 

                  <div class="form-group">                    
                    <div class="row">
                      <div class="col-md-6">
                        <label class="font-bold">Password <span class="text-danger">*[note: password min 6 char length]</span></label>
                        <input type="password" name="password" id="password" class="form-control" required placeholder="Enter Password">
                        @error('password')
                        <span class="text-danger font-weight-bold">{{$errors->first('password')}}</span>
                        @enderror
                      </div>
                      <div class="col-md-6">
                        <label class="font-bold">Confirm Password <span class="text-danger">*[note: password min 6 char length]</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Enter Password">
                      </div>

                      <div class="col-md-12">
                        <!-- <label class="col-sm-2 control-label font-bold">NID <span class="text-danger">*</span></label> -->
                        <label class="font-bold">NID</label>

                          <input type="file" name="nationalid" class="btn btn-warning form-control @if($errors->has('nationalid')) border-danger @else '' @endif nid">
                          @if($errors->has('nationalid'))
                            <label class="col-sm-12 font-bold text-danger control-label">{{$errors->first('nationalid')}}</label>

                          @else 
                            <label class="col-sm-12 font-bold control-label nid-white">[**Doc Type** jpeg, jpg, png|max:200kb]</label>
                          @endif
                        
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="row">
                      
                      <input type="hidden" name="usertype" id="usertype" value="client"/>
                      @error('usertype')
                      <span class="text-danger font-weight-bold">{{$errors->first('usertype')}}</span>
                      @enderror
                    </div>
                  </div>

                  <input type="hidden" name="paneltype" value="web"/>
                  
                  <button type="submit" class="btn btn-success btn-addon btn-md pull-right"><i class="fa fa-check"></i> Create Account</button>
                </form>
              </div>
            </div>
          </div>
       </div>
        <!-- /content -->
 
</div>


</div>

<script src="{{ asset('libs/jquery/jquery/dist/jquery.js') }}"></script>
<script src="{{ asset('libs/jquery/bootstrap/dist/js/bootstrap.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-load.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-jp.config.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-jp.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-nav.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-toggle.js') }}"></script>
<script src="{{ asset('smsapp/js/ui-client.js') }}"></script>

</body>
</html>