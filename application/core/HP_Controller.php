<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** Base Configuration **/
define('HP_PATH','third_party/hanzen_php');

/** Auto Load Class **/
function __autoload($class) {
	if(strpos($class, 'HP_') === 0){
		@include_once( APPPATH . 'core/'. $class . EXT );
	}
	else{
		@include_once(APPPATH.'controllers/'.strtolower($class).EXT);
	}
}
class HP_Controller extends CI_Controller{
public $module;
public $user;
public $exception = true;
	public function __CONSTRUCT(){
		parent::__CONSTRUCT();
		define('HP_VERSION','1.0.0');
		$this->load->add_package_path(APPPATH.HP_PATH);
		$this->load->library('msg');
		$this->load->helper(array('url','base'));
		$this->load->config('hanzen_php');
		log_message('debug', "HanzenPHP Initialized");
	}
	/**
	* remap execute controller
	**/
	public function _remap($method,$param){
		$this->_before();
		if(method_exists($this,$method)){
			call_user_func_array(array($this,$method),$param);
		}
		else{
			show_404();
		}
		$this->_after();
		$this->output->enable_profiler($this->config->item('enable_profiling'));
	}
	protected function _before(){
		return;
	}
	protected function _after(){
		return;
	}
	public function exception (){
		if($this->msg->is_error and $this->exception){
			throw new Exception('TERMINATE');
		}
		return true;
	}
}
/* Location: ./core/HZ_Controller.php */