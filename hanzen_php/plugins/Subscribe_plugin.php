<?php
class Subscribe_Plugin extends HP_Plugin{
	function __CONSTRUCT(){
		$this->load->database();
		$this->load->model('subscribe');
	}
	function register($name,$email){
		$this->load->library('validation');
		$rule = array(
			'name'	=> 'required|min_length[5]|max_length[50]'
			'email' => 'valid_email|is_unique[subscribe.email]|max_length[50]'
		);
		$data = array(
			'name' => $name,
			'email' => $email
		);
		if($this->validation->check($data,$rule)){
			$this->db->insert($this->db->dbprefix('subscribe'),array('email' => $email));
		}
	}
	function unregister($email){
		$this->load->library('validation');
		$rule = array(
			'email' => 'valid_email|is_exists[subscribe.email]'
		);
		$data = array(
			'email' => $email
		);
		if($this->validation->check($data,$rule)){
			$this->db->delete($this->db->dbprefix('subscribe'),$data);
		}
	}
	function remove_by_id($id){
		$this->db->delete($this->db->dbprefix('subscribe'),array('id' => $id));
	}
	function install(){
		$this->load->dbforge();
		$field = array(
			'id_subscribe' => array(
				'type' => 'INT',
				'constraint' => '8',
				'auto_increment' => true
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => '50'
			),
			'email' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
			)
		);
		$this->dbforge->add_field($field);
		$this->dbforge->add_key('id_subscribe', TRUE);
		$this->dbforge->create_table('subscribe', TRUE);
	}
}