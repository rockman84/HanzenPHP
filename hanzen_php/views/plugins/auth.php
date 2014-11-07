<div class="hp-login-form">
<form method="post" action="auth">
<input type="text" id="username" name="username" placeholder="Username" />
<input type="password" id="password" name="password" placeholder="Password"/>
<input type="submit" id="login-submit" value="Login"/>
<p><a href="#">Forgot password?</a></p>
</form>
<?php echo $this->session->flashdata('error_login');?>
</div> 