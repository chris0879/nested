<?php
session_start();
?>
<h1>home</h1>
<script>
// Facebook login with JavaScript SDK

function fbLogin() {
    FB.login(function(response) {
        if (response.authResponse) {
            console.log('Welcome!  Fetching your information.... ');
                FB.api('/me', function(response) {
                    console.log('Good to see you, ' + response.name + '.');
                });

        } else {
        console.log('User cancelled login or did not fully authorize.');
        }
    },{scope: 'user_birthday,user_location,user_friends,user_posts,user_gender,email,public_profile',return_scopes: true});
} 



// Logout from facebook
function fbLogout() {
   


    FB.logout(function(response) {
 
        console.log('Logout');
    });

}




function getLoginStatus () {

    FB.getLoginStatus(function(response) {
        if (response.status === 'connected') {
            console.log("response.status === 'connected'");
            // The user is logged in and has authenticated your
            // app, and response.authResponse supplies
            // the user's ID, a valid access token, a signed
            // request, and the time the access token 
            // and signed request each expire.
            var uid = response.authResponse.userID;
            var accessToken = response.authResponse.accessToken;
        } else if (response.status === 'not_authorized') {
            console.log("response.status === 'not_authorized'");
            // The user hasn't authorized your application.  They
            // must click the Login button, or you must call FB.login
            // in response to a user gesture, to launch a login dialog.
        } else {
            console.log('user not ');
            // The user isn't logged in to Facebook. You can launch a
            // login dialog with a user gesture, but the user may have
            // to log in to Facebook before authorizing your application.
        }
    });

}


function getPermission(){

    FB.getLoginStatus(function(response) {
        if (response.status === 'connected') {
            //var accessToken = response.authResponse.accessToken;
            FB.api('/me/permissions', function(response) {
            console.log(response);
            });

        }

        

    });

}




</script>

<!-- Facebook login or logout button -->
<a href="javascript:void(0);" onclick="fbLogin()" id="fbLink"><img src="fblogin.png"/>Login</a>
<a href="javascript:void(0);" onclick="fbLogout()" id="fbLink"><img src="fblogin.png"/>Logout</a>
<a href="javascript:void(0);" onclick="getLoginStatus()" id="fbLink"><img src="fblogin.png"/>Login Status</a>
<a href="javascript:void(0);" onclick="getPermission()" id="fbLink"><img src="fblogin.png"/>Get Permission</a>