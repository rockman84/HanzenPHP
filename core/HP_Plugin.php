<?php
/** Plugin Class **/
class HP_Plugin{
public $plugin_name;
	public function __CONSTRUCT(){
		$this->plugin_name = qcall($this,'get_class|strtolower');
		set_msg('Load Plugin '.$this->plugin_name,'','debug');
		log_message('debug', "Plugin Class Initialized");
	}
	/**
	 * get instance same as controller
	 */
	public function __get($key){
		
		$HP =& get_instance();
		return $HP->$key;
	}
	/**
	 * default view for plugin
	 */
	public function view($file = null,$data = array(),$return = true){
		
		$file = $file == null? substr($this->plugin_name,0,-7):$file;
		return $this->load->view('plugins/'.$file,$data,$return);
	}
}
/** End Plugin Class **/