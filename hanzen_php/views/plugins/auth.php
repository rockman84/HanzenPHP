<script src="https://apis.google.com/js/client:platform.js" async defer></script>

<span id="signinButton">
  <span
    class="g-signin"
    data-callback="signinCallback"
    data-clientid="959634305328-11q48flc2fjn8slrlv92mcjbn9e0gtbc.apps.googleusercontent.com"
    data-cookiepolicy="single_host_origin"
    data-requestvisibleactions="http://schema.org/AddAction"
    data-scope="https://www.googleapis.com/auth/plus.login">
  </span>
</span>

<script>
function signinCallback(authResult) {
  if (authResult['status']['signed_in']) {
    // Update the app to reflect a signed in user
    // Hide the sign-in button now that the user is authorized, for example:
    document.getElementById('signinButton').setAttribute('style', 'display: none');
  } else {
    // Update the app to reflect a signed out user
    // Possible error values:
    //   "user_signed_out" - User is signed-out
    //   "access_denied" - User denied access to your app
    //   "immediate_failed" - Could not automatically log in the user
    console.log(authResult);
  }
}

</script>
<div class="hp-login-form">
<form method="post" action="auth">
<input type="text" id="username" name="username" placeholder="Username" />
<input type="password" id="password" name="password" placeholder="Password"/>
<input type="submit" id="login-submit" value="Login"/>
<p><a href="#">Forgot password?</a></p>
</form>
<?php echo $this->session->flashdata('error_login');?>
</div> 