<?php
/** Msg Handling - Msg Class
* @author Hansen Wong, huang_hanzen@yahoo.co.id
* @copyright 2014 Hansen Wong
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 1.0.2
*/

class Msg {
public $HZ;
private $config = array(
	'error'	=> array(
		'set_error' => true,
		'log'	=> true,
	)
);
public $msg = array();
public $is_error = FALSE;
public $total_msg = 0;
public $convert = TRUE;
	public function __CONSTRUCT(){
		$this->HZ = & get_instance();
	}
	public function set_group($config){
		$this->config = array_merge($this->config,$config);
	}

	public function set($string, $replace = null, $index = 'error'){
		if(isset($this->config[$index])){
			$setup = $this->config[$index];
			$this->set_msg($string,$replace,$index,$setup['set_error']);
		}
		else{
			$this->set_msg($string,$replace,$index);
		}
	}
	public function set_msg($string, $replace = null, $group = 'error', $set_error = TRUE){
		$this->msg[$group][] = array(
			'index' => $string,
			'replace' => $replace
		);
		$this->total_msg = $this->total_msg + 1;
		if($set_error){$this->is_error = true;}
	}
	/* clear all msg */
	public function clear_msg($group = 'all'){
		if($group == 'all'){
			$this->msg = array();
			$this->is_error = FALSE;
			$this->total_msg = 0;
		}
		else{
			$count = count($this->msg[$group]);
			unset($this->msg[$group]);
			$this->total_msg = $this->total_msg - $count;
		}
	}
	/* error checking */
	public function is_error($group){
		if(count($this->msg[$group]) > 0){
			return TRUE;
		}
	}
	/* get all msg return array*/
	public function get_all(){
		$result = array(
			'msg' => $this->_convert_array($this->msg),
			'is_error' => $this->is_error,
			'total' => $this->total_msg
		);
		return $result;
	}
	/* run batch convert */
	protected function _convert_array($data){
		$msg = array();
		if(is_array($data)){
			foreach($data as $name => $val){
				foreach($val as $index){
					$msg[$name][] = string_replace($this->_convert($index['index']),$index['replace']);
				}
			}
		}
		return $msg;
	}
	/* run single convert */
	protected function _convert($source){
		if($this->convert){
			if(isset($this->HZ->lang->language[$source])){
				$source = $this->HZ->lang->language[$source];
			}
			else{
				$source = '['.$source.']';
			}
		}
		return $source;
	}
}
/** End of Msg Class **/
?>