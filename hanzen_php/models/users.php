<?php
class Users_Model extends HP_model{
	public $rule = array(
		'id_user' => 'primary[AI]',
		'email'	=> 'valid_email|required',
		'username'	=> 'required|is_unique[users.username]',
		'password'	=> 'min_length[6]|max_length[32]|required',
	);
}
