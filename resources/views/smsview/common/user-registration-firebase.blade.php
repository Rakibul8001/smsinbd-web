<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create a New Account</title>
 <script src="https://www.gstatic.com/firebasejs/5.2.0/firebase.js"></script>
  <script>
    // Initialize Firebase
   var config = {
    apiKey: "AIzaSyDg5hiE_4TuzfqLCVHxyvhFF0Rd1pCoqAY",
    authDomain: "sms-in-bd.firebaseapp.com",
    databaseURL: "https://sms-in-bd.firebaseio.com",
    projectId: "sms-in-bd",
    storageBucket: "sms-in-bd.appspot.com",
    messagingSenderId: "1000227735803"
  };
  firebase.initializeApp(config);
  
	
  </script>
  <script src="https://cdn.firebase.com/libs/firebaseui/2.3.0/firebaseui.js"></script>
  <link type="text/css" rel="stylesheet" href="https://cdn.firebase.com/libs/firebaseui/2.3.0/firebaseui.css" />
  <link href="{{asset('css/style_firebase.css')}}" rel="stylesheet" type="text/css" media="screen" />
   
</head>
<body>
    <div id="container">
    <div align="center" id="logo">  </div>
      <div id="loading">Loading...</div>
      <div id="loaded" class="hidden">
        <div id="main">
          <div id="user-signed-in" class="hidden">
             <h3>Create a New Account</h3>
            <div id="user-info">
              <!--<div id="photo-container">
                <img id="photo">
              </div>
              <div id="name"></div>
              <div id="email"></div>
              <div id="phone"></div>-->
              <div class="clearfix"></div>
               <div id ="content"> </div>
                <div class="clearfix"></div>
            </div>
            <p> 
              <!--<button id="sign-out">Sign Out</button>
              <button id="delete-account">Delete account</button>-->
            </p>
          </div>
          <div id="user-signed-out" class="hidden">
            
            <div id="firebaseui-spa">
              <h3>Create a New Account</h3>
              <div id="firebaseui-container"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
	<script src="{{asset('js/jquery-2.1.4.min.js')}}"></script>
    <script src="{{asset('js//app_firebase.js')}}"></script>
     
</body>
</html>