<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Hanzen PHP
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		Hanzen PHP
 * @author		Wong Hansen
 * @copyright	Copyright (c) 2014, Wong Hansen.
 * @since		Version 1.0.1
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Hanzen PHP Application Controller Class
 *
 * This class object is the super class that every library in
 * Hanzen PHP will be assigned to.
 *
 * @package		Hanzen PHP
 * @subpackage	Core
 * @category	Core
 * @author		Wong Hansen
 */
 
/** Path HanzenPHP directory folder **/
define('HP_PATH',APPPATH.'third_party/hanzen_php/');

/** Hanzen PHP Version **/
define('HP_VERSION','1.0.3');
/** Auto Load Class
 * auto load file necessary when extend class
 */
function __autoload($class) {
	if(strpos($class, 'HP_') === 0){
		foreach(array(APPPATH, HP_PATH) as $path){
			if(file_exists($path . 'core/' . $class . EXT)){
				@include_once( $path . 'core/' . $class . EXT );
				break;
			}
		}
	}
	else{
		@include_once(APPPATH.'controllers/'.strtolower($class).EXT);
	}
}
/* End auto load class */

/** HanzenPHP Controller **/
class HP_Controller extends CI_Controller{
public $title;
public $plugin;
	public function __CONSTRUCT(){
		parent::__CONSTRUCT();
		/* initialize default hanzen packages */
		$this->plugin = new stdClass();
		$this->model = new stdClass();
		$this->title = 'HanzenPHP (Extend Version)';
		$this->load->add_package_path(HP_PATH);
		/** Load Config **/
		$this->load->config('hanzen_php');
		date_default_timezone_set(config_item('default_timezone'));
		$this->load->library('msg');
		$this->load->helper(array('url','base'));
		log_message('debug', "HanzenPHP Initialized");
		set_msg('Load Controller : '.get_class($this),'','debug');
	}
	/**
	* remap execute controller
	**/
	public function _remap($method,$param){
		if(method_exists($this,$method)){
			$this->_before();
			call_user_func_array(array($this,$method),$param);
			$this->_after();
		}
		else{
			show_404();
		}
		$this->output->enable_profiler($this->config->item('show_profiling'));
		if(config_item('show_message_debug')){
			$this->load->view('hp_message_debug',$this->msg->get_all());
		}
	}
	/**
	 * execute pre controller
	 */
	protected function _before(){
		return;
	}
	/**
	 * execute after controller
	 */
	protected function _after(){
		return;
	}
}
/* Location: ./core/HP_Controller.php */