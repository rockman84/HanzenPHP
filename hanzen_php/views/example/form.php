<h2>Validation</h2>
<form method="post" action="">
<input type="text" name="email" placeholder="email address">
<?php echo ul($this->validation->get_error('email')); ?>
<input type="text" name="ip_addr" placeholder="ip addrress">
<?php echo ul($this->validation->get_error('ip_addr')); ?>
<input type="submit" value="Submit">
</form>