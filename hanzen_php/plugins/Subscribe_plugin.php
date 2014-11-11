<?php
class Subscribe_Plugin extends HP_Plugin{
	function __CONSTRUCT(){
		$this->load->database();
		$this->load->model('subscribe');
	}
	function register($name,$email){
		$label = array(
			'name' => 'Full Name',
			'email' => 'E-Mail Address'
		);
		get_library('validation')->set_language()->set_label($label);
		$data = array(
			'name' => $name,
			'email' => $email
		);
		get_model('subscribe')->create($data);
	}
	function unregister($email){
		$data = array(
			'email' => $email
		);
		$rule = array(
			'email' => 'is_exists[subscribe.email]'
		);
		if(get_library('validation')->set_language()->check($data,$rule)){
			get_model('subscribe')->delete($data);
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