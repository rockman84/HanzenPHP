<?php
/** Msg Handling - Msg Class
* @author Hansen Wong, huang_hanzen@yahoo.co.id
* @copyright 2014 Hansen Wong
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 1.0.3
*/
class Msg {
public $HZ;
/**
* set group message
**/
private $config = array(
	'error'	=> array('set_error' => true),
	'form_error' => array('set_error' => true),
	'info'	=> array('set_error' => false),
	'debug'	=> array('set_error' => false)
);
public $msg = array();
public $is_error = FALSE;
public $convert = TRUE;
	public function __CONSTRUCT(){
		$this->HZ = & get_instance();
	}
	/**
	 * add new group message
	 *
	 * @param array
	 * @return void
	 **/
	public function set_group($config){
		$this->config = array_merge($this->config,$config);
		return $this;
	}
	/**
	 * quick Set storage message
	 *
	 * @param string
	 * @param string
	 * @param string
	 * @return void
	 **/
	public function set($string, $replace = null, $index = 'error'){
		if(isset($this->config[$index])){
			$setup = $this->config[$index];
			$this->set_msg($string,$replace,$index,$setup['set_error']);
		}
		else{
			$this->set_msg($string,$replace,$index);
		}
		return $this;
	}
	/**
	 * Set storage message by manual
	 * ```
	 * [key]string
	 * ```
	 * @param string
	 * @param string
	 * @param string - group name
	 * @param boolean - this message is error?
	 * @return void
	 */
	public function set_msg($string, $replace = null, $group = 'error', $set_error = TRUE){
		$key = null;
		if(preg_match("/\[(.*)\](.*)/",$string, $match)){
			$key = $match[1];
			$string = $match[2];
		}
		$msg = array(
			'key' => $key,
			'index' => $string,
			'replace' => $replace
		);
		$this->msg[$group][] = $msg;
	
		if($set_error){$this->is_error = true;}
		return $this;
	}
	/**
	 * clear all storage msg
	 *
	 * @param string
	 * @return void
	 */
	public function clear_msg($group = 'all'){
		if($group == 'all'){
			$this->msg = array();
			$this->is_error = FALSE;
		}
		else{
			$count = count($this->msg[$group]);
			unset($this->msg[$group]);
		}
		return $this;
	}
	/**
	 * get message
	 *
	 * @param string (default error)
	 * @return array
	 **/
	public function get($group = 'error'){
		$msg = array();
		if(isset($this->msg[$group])){
			$msg = $this->_convert($this->msg[$group]);
		}
		return $msg;
	}
	/* get all msg return array
	@return array
	*/
	public function get_all(){
		$result['msg'] = array();
		foreach($this->msg as $group => $val){
			$result['msg'][$group] = $this->_convert($val);
		}
		$result['is_error'] = $this->is_error;
		return $result;
	}
	/* run batch convert */
	protected function _convert($data){
		$msg = array();
		foreach($data as $m){
			$d = string_replace($this->_get_lang($m['index']),$m['replace']);
			if($m['key'] !== null){
				$msg[strtoupper($m['key'])] = $d;
			}
			else{
				$msg[] = $d;
			}
		}
		return $msg;
	}
	/* get_language */
	protected function _get_lang($source){
		if($this->convert){
			if(isset($this->HZ->lang->language[$source])){
				$source = $this->HZ->lang->language[$source];
			}
		}
		return $source;
	}
}

// END Msg class

/* End of file Msg.php */
/* Location: ./hanzen_php/libraries/Msg.php */