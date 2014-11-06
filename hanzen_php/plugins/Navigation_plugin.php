<?php
class Navigation_Plugin extends HP_Plugin {
	public function __CONSTRUCT(){
		parent::__CONSTRUCT();
		$this->load->helper('html');
	}
	public function menu($data = array()){
		echo ul(array('hansen','yetty' => 'ya','budi'=>array('danny','lia')),array('id' => 'list'));
	}
}
?>