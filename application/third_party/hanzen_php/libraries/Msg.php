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
	/* get msg by group */
	public function get_msg($group = 'error'){
		if(!isset($this->msg[$group])){
			$msg = array();
		}
		else{
			$msg = $this->msg[$group];
		}
		$result = array(
			'msg' => $msg,
			'group'	=> $group,
			'is_error' => $this->is_error,
			'total' => count($msg)
		);
		return $result;
	}
	/* get all msg return array*/
	public function get_all(){
		$result = array(
			'msg' => $this->convert_array($this->msg),
			'is_error' => $this->is_error,
			'total' => $this->total_msg
		);
		return $result;
	}
	/* get all / group message return html/string */
	public function get_html($msg = false){
		$data = $msg?$msg:$this->get_all();
		$html= '';
		foreach($data['msg'] as $name => $val){
			if(is_array($val)){
				$html .= '<h3>'.$name.'</h3><ul>';
				foreach($val as $value){
					$html .= '<li>'.$value.'</li>';
				}
				$html .= '</ul>';
			}
			else{
				$html .='<li>'.$val.'</li>';
			}
		}
		return '<div id="messages">'.$html.'</div>';
	}
	/* run batch convert */
	protected function convert_array($data){
		$msg = array();
		if(is_array($data)){
			foreach($data as $name => $val){
				foreach($val as $index){
					$msg[$name][] = $this->replace($this->convert($index['index']),$index['replace']);
				}
			}
		}
		return $msg;
	}
	/* run single convert */
	protected function convert($source){
		if($this->convert){
			if(isset($this->HZ->lang->language[$source])){
				$source = $this->HZ->lang->language[$source];
			}
			else{
				$source = '['.$source.']';
				log_message('info',text('MISSING_LANGUAGE',array($source)));
			}
		}
		return $source;
	}
	/* replace index msg to langguage */
	public function replace($string,$replace){
		if(is_array($replace)){
			foreach($replace as $name => $val){
				$word['find'][]= '{'.$name.'}';
				$word['replace'][] = $val;
			}
			return str_replace($word['find'],$word['replace'],$string);
		}
		else{
			return $string;
		}
	}
}
/** End of Msg Class **/
?>