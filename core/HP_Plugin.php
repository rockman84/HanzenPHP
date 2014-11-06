<?php
/** Plugin Class **/
class HP_Plugin{
public $plugin_name;
	public function __CONSTRUCT(){
		log_message('debug', "Plugin Class Initialized");
	}
	/**
	 * get instance same as controller
	 */
	public function __get($key){
		$this->plugin_name = qcall($this,'get_class|strtolower');
		$HP =& get_instance();
		return $HP->$key;
	}
	/**
	 * default view for plugin
	 */
	public function view($file = null,$data = array(),$return = false){
		$file = $file == null? substr($this->plugin_name,0,-7):$file;
		$this->load->view('plugins/'.$file,$data,$return);
	}
}
/** End Plugin Class **/