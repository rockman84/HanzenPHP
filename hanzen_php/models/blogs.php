<?php
class Blogs_Model extends HP_model{
	public $rule = array(
		'id_subscribe' => 'primary[AI]',
		'email'	=> 'valid_email|required|is_unique[subscribe.email]',
		'name'	=> 'min_length[4]|max_length[50]|required',
	);
}