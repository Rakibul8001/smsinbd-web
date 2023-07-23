<!DOCTYPE html>
<html lang="en" class="">
<head>
  <meta charset="utf-8" />
  <title>
  Reseller SMS Panel ||  smsinbd.com
  </title>
  <meta name="description" content="Professional SMS Broadcast Panel. smsinbd.com is a project of Data Host IT" />
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
  </style>

</head>
<body style="background-color:#222;">
<div class="app app-header-fixed ">
  

<div class="container w-xxl w-auto-xs panel" style="    background-color: #2c2323; box-shadow: 0px 1px 3px #000;">
    <img src="{{ asset('images/logo.png')}}" style="left:46%; right:0; position:relative; margin-left:auto; margin-right:auto; margin-top:10px;"/>
  <a href class="navbar-brand block m-t">Reseller Login</a>
  <div class="m-b-lg">
    <!-- <div class="wrapper text-center">
      <strong>Sign in to get in touch</strong>
    </div> -->

    @if(session()->has('errmsg'))
    <div class="wrapper text-center">
      <strong class="text-danger">{{session()->get('errmsg')}}</strong>
    </div>
    @endif

<form name="form" class="form-validation" action="{{route('smslogin')}}" method="POST">
      @csrf
      <div class="text-danger wrapper text-center">
          
      </div>
      <div class="list-group list-group-sm">
        <div class="list-group-item m-b">
            <input type="password" name="resetpassword" id="resetpassword" placeholder="New Password" class="form-control no-border" required>
        </div>
        
      </div>
      <button type="submit" class="btn btn-lg btn-primary btn-block">Reset New Password</button>
      
      <!-- <a href="{{route('user-registration')}}" class="btn btn-lg btn-default btn-block">Create an account</a> -->
      <!-- <div class="text-center m-t m-b"><a ui-sref="access.forgotpwd">Forgot password?</a></div> -->
      <div class="line line-dashed"></div>
      <!-- <p class="text-center"><small>Do not have an account?</small></p> -->
    </form>
  </div>
 
</div>


</div>

<script src="{{ asset('libs/jquery/jquery/dist/jquery.js') }}"></script>
<script src="{{ asset('libs/jquery/bootstrap/dist/js/bootstrap.js') }}"></script>
</body>
</html>
