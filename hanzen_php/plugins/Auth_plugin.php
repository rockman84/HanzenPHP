<?php
class Auth_Plugin extends HP_Plugin {
	public function __CONSTRUCT(){
		$this->load->library(array('session'));
	}
	public function login($username, $password){
		$this->db->get($this->db->dbprefix().'users');
		$this->session->set_userdata('username',$username);
	}
	public function destroy($callback = ''){
		$this->session->sess_destroy();
		if(is_callable($callback)){
		
		}
	}
	public function is_login(){
		if($this->session->userdata('username')){
			return true;
		}
	}
	public function change_password($old_password, $new_password){
		if($old_password){
			// update password
		}
	}
	public function register(){
	
	}
	public function activated($email,$key){
	
	}
	public function install(){
		$table_name = $this->db->dbprefix().'users';
		$field = array(
			'id_user' => array(
				'type' => 'INTEGER',
				'constraint' => 10,
				'auto_increment' => TRUE
			),
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => 15
			),
			'password' => array(
				'type' => 'VARCHAR',
				'constraint' => 32
			)
		);
		$this->load->dbforge();
		$this->dbforge->create_table($table_name,TRUE);
		$this->dbforge->add_field($field);
	}
}
?>