<?php if ( ! defined('HP_PATH')) exit('require HanzenPHP package');
/** Validation Class
* modified from Form_validation library
* @author Hansen Wong, huang_hanzen@yahoo.co.id
* @copyright 2014 Hansen Wong
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 1.0
*/
class Validation {
public $HP;
public $data = array();
public $rules = array();
public $primary = null;
public $update = false;
public $error = false;
public $msg = array();
	public function __construct(){
		$this->HP = & get_instance();
		$this->HP->lang->load('validation');
		// Set the character encoding in MB.
		if (function_exists('mb_internal_encoding')){
			mb_internal_encoding($this->HP->config->item('charset'));
		}
	}
	public function set($data,$rule = array(),$base_on_rule = true){
		$this->rules = $rule;
		if(is_array($this->rules) and is_array($data)){
			/* mapping base on rules */
			if($base_on_rule){
				foreach ($this->rules as $field => $val){
					$value = isset($data[$field])?$data[$field]:'';
					$this->data [$field] = array(
						'rule' 	=> $val,
						'value' => $value,
						'field' => $field
					);
				}
			}
			/* mapping base on data */
			else{
				foreach($data as $field => $val){
					if(isset($this->rules[$field])){
						$this->data[$field] = array(
							'rule'  => $this->rules[$field],
							'value' => $val,
							'field' => $field,
						);
					}
				}
			}
		}
	}
	public function check($data,$rule = array(),$base_on_rule = true){
		/* clean data */
		$this->data = array();
		/* mapping validation */
		$this->set($data,$rule,$base_on_rule);
		/* run checking */
		$this->run();
		/* result checking */
		return !$this->HP->msg->is_error;
	}
	public function run(){
		$this->HP->lang->open('validation');
		/** here we go!...**/
		if(count($this->data)){
			foreach($this->data as $data){
				foreach(explode('|',$data['rule']) as $role){
					$param = false;
					if (preg_match("/(.*?)\[(.*)\]/",$role, $match)){
						$rule	= $match[1];
						$param	= $match[2];
					}
					else{
						$rule = $role;
					}
					/* check callback */
					$callback = false;
					if(substr($rule,0,5)=='call_'){
						$rule = substr($rule,5);
						$call = explode(':',$rule);
						$total = count($call);
						if($total==1){
							$callback = 'parent';
						}
						elseif($total == 2){
							$callback = 'class';
						}
					}
					/** no callback **/
					if(!$callback){
						if(method_exists($this,$rule)){
							if(!$this->$rule($data['value'],$param,$data['field'])){
								set_msg(strtoupper($rule),array(ucwords(str_replace('_',' ',$data['field'])),$param));
								$this->error = true;
							}
						}
						else{
							show_error('The rule '.$rule.' not found');
							$this->error = true;
						}
					}
					/** using callback **/
					else{
						/** call_method **/
						if($callback == 'parent' and method_exists($this->HP,$call[0])){
							if(!$this->HP->$call[1]($data['value'],$param)){
								set_msg(strtoupper($call[0]),array($data['field'],$param));
								$this->error = true;
							}
						}
						/** call_class:method **/
						elseif($callback == 'class' and method_exists($this->HP->$call[0],$call[1])){
							if(!$this->HP->$call[0]->$call[1]($data['value'],$param)){
								set_msg(strtoupper($call[1]),array($data['field'],$param));
								$this->error = true;
							}
						}
						else{
							set_msg('METHOD_NOT_FOUND',$call,'info');
							$this->error = true;
						}
					}
				}
			}
		}
		/** No have data **/
		else{
			set_msg('NO_INPUT_DATA');
		}
	}
	public function set_error($field,$rule){
		if(!isset($this->msg[$field])){
			$this->msg[$field] = array();
		}
		$this->msg[$field][$rule] = '';
	}
	public function none(){
		return true;
	}
	public function required($str){
		if ( ! is_array($str)){
			return (trim($str) == '') ? FALSE : TRUE;
		}
		else{
			return ( ! empty($str));
		}
	}
	public function regex_match($str, $regex){
		if ( ! preg_match($regex, $str)){return FALSE;}
		return  TRUE;
	}
	public function matches($str, $field){
		if ( ! isset($_POST[$field])){
			return FALSE;
		}
		$field = $_POST[$field];
		return ($str !== $field)? FALSE : TRUE;
	}
	/** primary[AI] / [none:table.field] **/
	public function primary($str,$params,$field){
		$param = explode(':', $params);
		if($param[0]!='AI' AND !empty($str)){
			return $this->is_unique($str,$param[1]);
		}
		$this->primary = $field;
		return true;
	}
	public function is_unique($str, $field){
		list($table, $field)=explode('.', $field);
		if($this->update){
			$this->HP->db->where(array($this->primary.' !='=>$this->data[$this->primary]['value']));
		}
		$query = $this->HP->db->limit(1)->get_where($this->HP->db->dbprefix($table), array($field => $str));
		
		return $query->num_rows() === 0;
    }
	public function is_exists($str, $field){ 
		list($table, $field)=explode('.', $field);
		$query = $this->HP->db->limit(1)->get_where($this->HP->db->dbprefix($table), array($field => $str));
		return $query->num_rows() === 1;
	}
	public function min_length($str, $val){
		if($str){
			if (preg_match("/[^0-9]/", $val)){return FALSE;}
			if (function_exists('mb_strlen')){
				return (mb_strlen($str) < $val) ? FALSE : TRUE;
			}
			return (strlen($str) < $val) ? FALSE : TRUE;
		}
		else{
			return true;
		}
	}
	public function max_length($str, $val){
		if (preg_match("/[^0-9]/", $val)){
			return FALSE;
		}
		if (function_exists('mb_strlen')){
			return (mb_strlen($str) > $val) ? FALSE : TRUE;
		}
		return (strlen($str) > $val) ? FALSE : TRUE;
	}
	public function exact_length($str, $val){
		if (preg_match("/[^0-9]/", $val)){return FALSE;}
		if (function_exists('mb_strlen')){
			return (mb_strlen($str) != $val) ? FALSE : TRUE;
		}
		return (strlen($str) != $val) ? FALSE : TRUE;
	}
	public function valid_email($str){
		if($str){
			return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
		}
		else{return TRUE;}
	}
	public function valid_emails($str){
		if (strpos($str, ',') === FALSE)
		{
			return $this->valid_email(trim($str));
		}

		foreach (explode(',', $str) as $email)
		{
			if (trim($email) != '' && $this->valid_email(trim($email)) === FALSE)
			{
				return FALSE;
			}
		}

		return TRUE;
	}
	public function valid_ip($ip, $which = ''){
		return $this->HP->input->valid_ip($ip, $which);
	}
	public function alpha($str){
		return ( ! preg_match("/^([a-z])+$/i", $str)) ? FALSE : TRUE;
	}
	public function alpha_numeric($str){
		return ( ! preg_match("/^([a-z0-9])+$/i", $str)) ? FALSE : TRUE;
	}
	public function alpha_dash($str){
		return ( ! preg_match("/^([-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
	}
	public function numeric($str){
		if($str){return (bool)preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str);}
		else{return TRUE;}
	}
	public function is_numeric($str){
		return ( ! is_numeric($str)) ? FALSE : TRUE;
	}
	public function integer($str){
		return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
	}
	public function decimal($str){
		return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
	}
	public function greater_than($str, $min){
		if ( ! is_numeric($str)){return FALSE;}
		return $str > $min;
	}
	public function less_than($str, $max){
		if ( ! is_numeric($str)){
			return FALSE;
		}
		return $str < $max;
	}
	public function is_natural($str){
		return (bool) preg_match( '/^[0-9]+$/', $str);
	}
	public function is_natural_no_zero($str){
		if ( ! preg_match( '/^[0-9]+$/', $str)){return FALSE;}
		if ($str == 0){	return FALSE;}
		return TRUE;
	}
	public function valid_base64($str){
		return (bool) ! preg_match('/[^a-zA-Z0-9\/\+=]/', $str);
	}
	public function prep_for_form($data = ''){
		if (is_array($data)){
			foreach ($data as $key => $val){
				$data[$key] = $this->prep_for_form($val);
			}
			return $data;
		}
		if ($this->_safe_form_data == FALSE OR $data === ''){
			return $data;
		}
		return str_replace(array("'", '"', '<', '>'), array("&#39;", "&quot;", '&lt;', '&gt;'), stripslashes($data));
	}
	public function prep_url($str = ''){
		if ($str == 'http://' OR $str == ''){
			return '';
		}
		if (substr($str, 0, 7) != 'http://' && substr($str, 0, 8) != 'https://'){
			$str = 'http://'.$str;
		}
		return $str;
	}
	public function strip_image_tags($str){
		return $this->HP->input->strip_image_tags($str);
	}
	public function xss_clean($str){
		return $this->HP->security->xss_clean($str);
	}
	public function encode_php_tags($str){
		return str_replace(array('<?php', '<?PHP', '<?', '?>'),  array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $str);
	}
}
/* Location: ./libraries/Validation.php */