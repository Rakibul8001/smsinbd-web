<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
         <!-- Insert these scripts at the bottom of the HTML, but before you use any Firebase services -->

          <!-- Firebase App (the core Firebase SDK) is always required and must be listed first -->
          <script src="https://www.gstatic.com/firebasejs/7.19.1/firebase-app.js"></script>

        <!-- If you enabled Analytics in your project, add the Firebase SDK for Analytics -->
        <script src="https://www.gstatic.com/firebasejs/7.19.1/firebase-analytics.js"></script>

        <!-- Add Firebase products that you want to use -->
        <script src="https://www.gstatic.com/firebasejs/7.19.1/firebase-auth.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.19.1/firebase-firestore.js"></script>
        <script>
          // Your web app's Firebase configuration
          var firebaseConfig = {
            apiKey: "AIzaSyBAxdwrrhUbXPVm5pFwwJHHjYEYCql92sY",
            authDomain: "userauthentication-751ba.firebaseapp.com",
            databaseURL: "https://userauthentication-751ba.firebaseio.com",
            projectId: "userauthentication-751ba",
            storageBucket: "userauthentication-751ba.appspot.com",
            messagingSenderId: "908439326566",
            appId: "1:908439326566:web:f58159850c1f3a94d482a7"
          };
          // Initialize Firebase
          firebase.initializeApp(firebaseConfig);
        </script>

        <script src="https://cdn.firebase.com/libs/firebaseui/3.5.2/firebaseui.js"></script>
        <link type="text/css" rel="stylesheet" href="https://cdn.firebase.com/libs/firebaseui/3.5.2/firebaseui.css" />

</head>
<body>
  <div id="firebaseui-auth-container"></div>
  <div id="loader">Loading...</div>


<script>

  var ui = new firebaseui.auth.AuthUI(firebase.auth());

  var uiConfig = {
  callbacks: {
      signInSuccessWithAuthResult: function(user, authResult, redirectUrl) {
        // User successfully signed in.
        // Return type determines whether we continue the redirect automatically
        // or whether we leave that to developer to handle.
        handleSignedInUser(user);
        return false;
      },
      uiShown: function() {
        // The widget is rendered.
        // Hide the loader.
        document.getElementById('loader').style.display = 'none';
      }
      },
      // Will use popup for IDP Providers sign-in flow instead of the default, redirect.
      signInFlow: 'popup',
      signInSuccessUrl: '<url-to-redirect-to-on-success>',
      signInOptions: [
        // Leave the lines as is for the providers you want to offer your users.
        {
          provider: firebase.auth.PhoneAuthProvider.PROVIDER_ID,
          defaultCountry: 'BD',
          defaultNationalNumber: '',
          loginHint: '+880'
        }
      ],
      // Terms of service url.
      tosUrl: 'https://www.google.com',
      // Privacy policy url.
      //privacyPolicyUrl: '<your-privacy-policy-url>'
};

ui.start('#firebaseui-auth-container', uiConfig);


/**
 * Displays the UI for a signed in user.
 * @param {!firebase.User} user
 */
var handleSignedInUser = function(user) { //alert(user.phoneNumber)
  console.log(user);
  user.getIdToken().then(function(accessToken) {
    if (user.phoneNumber) {
      window.location.href = 'http://login.smsinbd.com/check-firebase-client?phone='+user.phoneNumber+'&uid='+user.uid+'&token='+accessToken;
    }
  
  });
};


var handleSignedOutUser = function() {
  ui.start('#firebaseui-container', uiConfig);
   
};


firebase.auth().onAuthStateChanged(function(user) {
  user ? handleSignedInUser(user) : handleSignedOutUser();
});


/**
 * Deletes the user's account.
 */
var deleteAccount = function() {
  firebase.auth().currentUser.delete().catch(function(error) {
    if (error.code == 'auth/requires-recent-login') {
      // The user's credential is too old. She needs to sign in again.
      firebase.auth().signOut().then(function() {
        // The timeout allows the message to be displayed after the UI has
        // changed to the signed out state.
        setTimeout(function() {
          alert('Please sign in again to delete your account.');
        }, 1);
      });
    }
  });
};


/**
 * Initializes the app.
 */
var initApp = function() {
  document.getElementById('sign-out').addEventListener('click', function() {
    firebase.auth().signOut();
  });
  document.getElementById('delete-account').addEventListener(
      'click', function() {
        deleteAccount();
      });
};
</script>


</body>
</html>