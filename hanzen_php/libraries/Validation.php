<?php if ( ! defined('HP_PATH')) exit('require HanzenPHP package');
/** Validation Class
* modified from Form_validation library
* @author Hansen Wong, huang_hanzen@yahoo.co.id
* @copyright 2014 Hansen Wong
* @version 1.2

@example :
	$rule = array(
		'email' => 'min_length[5]|valid_email|max_length[30]|required|strtoupper',
		'ip_addr' => 'required|valid_ip|strtoupper'
	);
	if(get_library('validation')->set_language()->check($_POST,$rule)){
		// do something when valid
	}
*/
class Validation {
public $HP;
public $label;
public $data = array();
public $rules = array();
public $primary = null;
public $update = false;
public $error = false;
public $msg = array();
public $flash_data = false;
	public function __CONSTRUCT(){
		$this->HP = & get_instance();
		// Set the character encoding in MB.
		if (function_exists('mb_internal_encoding')){
			mb_internal_encoding($this->HP->config->item('charset'));
		}
	}
	/**
	 * Check validation input
	 *
	 * @param array
	 * @param array
	 * @param boolean
	 * @return Boolean
	 */
	public function check($data,$rule = array(),$base_on_rule = true){
		/* clean data */
		$this->data = array();
		$this->error = false;
		$this->msg = array();
		/* mapping validation */
		$this->_set($data,$rule,$base_on_rule);
		/* run checking */
		$this->_run();
		/* result checking */
		return !$this->error;
	}
	/**
	 * get error message
	 * 
	 * @param string
	 * @return array
	 **/
	public function get_error($field = null){
		if($field != null AND isset($this->msg[$field])){
			return $this->msg[$field];
		}
		elseif($field == null){
			return $this->msg;
		}
		return array();
	}
	/**
	 * set language for error message
	 *
	 * @param string - file name in language folder
	 * @return void
	 **/
	public function set_language($file = 'validation'){
		$this->HP->lang->load($file);
		return $this;
	}
	/**
	 * Set field for replace label name
	 *
	 * @param array
	 * @return void
	 **/
	public function set_label($data){
		$this->label = $data;
		return $this;
	}
	protected function _set($data,$rule = array(),$base_on_rule = true){
		$this->rules = $rule;
		if(is_array($this->rules) and is_array($data)){
			/* mapping base on rules */
			if($base_on_rule){
				foreach ($this->rules as $field => $val){
					$value = isset($data[$field])?$data[$field]:'';
					$this->data [$field] = array(
						'rule' 	=> $val,
						'value' => $value,
						'field' => $field,
						'label' => null
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
							'label' => null
						);
					}
				}
			}
			/* set label */
			if(is_array($this->label)){
				foreach($this->data as $data){
					if(isset($this->label[$data['field']])){
						$this->data[$data['field']]['label'] = $this->label[$data['field']];
					}
				}
			}
		}
	}
	protected function _run(){
		/** here we go!...**/
		if(count($this->data)){
			/** analyst all data **/
			foreach($this->data as $data){
				/** analyst field data **/
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
								$this->_set_error($rule,$param,$data['field'],$data['value'],$data['label']);
							}
						}
						elseif(function_exists($rule)){
							$prep = $rule($this->data[$data['field']]['value']);
							if(is_bool($prep)){
								if($prep){
									$this->_set_error($rule,$param,$data['field'],$data['value'],$data['label']);
								}
							}
							else{
								$this->data[$data['field']]['value'] = $prep;
							}
						}
						else{
							show_error('The rule '.$rule.' not found');
						}
					}
					/** using callback **/
					else{
						/** call_method **/
						if($callback == 'parent' and method_exists($this->HP,$call[0])){
							if(!$this->HP->$call[1]($data['value'],$param)){
								$this->_set_error($call[0],$param,$data['field'],$data['value'],$data['label']);
							}
						}
						/** call_class:method **/
						elseif($callback == 'class' and method_exists($this->HP->$call[0],$call[1])){
							if(!$this->HP->$call[0]->$call[1]($data['value'],$param)){
								$this->_set_error($call[1],$param,$data['field'],$data['value'],$data['label']);
							}
						}
						else{
							show_error('Method '.$call.' not found');
						}
					}
				}
				/** if error and flash_data true set flash data **/
				if($this->flash_data){
					$this->HP->session->set_flashdata($data['field'],$this->get_error($data['field']));
				}
			}
			if($this->error){
				set_msg('INVALID_FORM');
			}
		}
		/** No have data **/
		else{
			set_msg('NO_INPUT_DATA');
		}
	}
	/** set error message **/
	protected function _set_error($rule,$param,$field,$value,$label){
		$this->error = true;
		if(!isset($this->msg[$field])){
			$this->msg[$field] = array();
		}
		$lang = $this->HP->lang->line(strtoupper($rule));
		$lang = $lang == ''?'['.$rule.']':$lang;
		if($label== null){
			$label = $field;
		}
		/** {0} = label / field, {1} = param, {2} = value, {3} rule **/
		$this->msg[$field][] = string_replace($lang, array(ucwords(str_replace('_',' ',$label)),$param,$rule,$value));
	}
	/**
	 * nothing do
	 * @return true
	 **/
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
	/**
	 * regex_match[%^]
	 **/
	public function regex_match($str, $regex){
		if ( ! preg_match($regex, $str)){return FALSE;}
		return  TRUE;
	}
	/**
	 * matches[field_name]
	 **/
	public function matches($str, $field){
		if ( ! isset($_POST[$field])){
			return FALSE;
		}
		$field = $_POST[$field];
		return ($str !== $field)? FALSE : TRUE;
	}
	/**
	 * primary[AI] -  attribute is primary key and auto increment
	 * primary[none:table_name.field_name] - attribute is primary key without auto increment
	 *
	 **/
	public function primary($str,$params,$field){
		$param = explode(':', $params);
		if($param[0]!='AI' AND !empty($str)){
			return $this->is_unique($str,$param[1]);
		}
		$this->primary = $field;
		return true;
	}
	/**
	 * is_unique[table_name.field_name]
	 **/
	public function is_unique($str, $field){
		list($table, $field)=explode('.', $field);
		if($this->update){
			$this->HP->db->where(array($this->primary.' !='=>$this->data[$this->primary]['value']));
		}
		$query = $this->HP->db->limit(1)->get_where($this->HP->db->dbprefix($table), array($field => $str));
		
		return $query->num_rows() === 0;
    }
	/**
	 * is_exists[table_name.field_name]
	 **/
	public function is_exists($str, $field){ 
		list($table, $field)=explode('.', $field);
		$query = $this->HP->db->limit(1)->get_where($this->HP->db->dbprefix($table), array($field => $str));
		return $query->num_rows() === 1;
	}
	/**
	 * Minimum Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	public function min_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) < $val) ? FALSE : TRUE;
		}

		return (strlen($str) < $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Max Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	public function max_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) > $val) ? FALSE : TRUE;
		}

		return (strlen($str) > $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Exact Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	public function exact_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) != $val) ? FALSE : TRUE;
		}

		return (strlen($str) != $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Email
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function valid_email($str)
	{
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Emails
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function valid_emails($str)
	{
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

	// --------------------------------------------------------------------

	/**
	 * Validate IP Address
	 *
	 * @access	public
	 * @param	string
	 * @param	string "ipv4" or "ipv6" to validate a specific ip format
	 * @return	string
	 */
	public function valid_ip($ip, $which = '')
	{
		return $this->HP->input->valid_ip($ip, $which);
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha($str)
	{
		return ( ! preg_match("/^([a-z])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha_numeric($str)
	{
		return ( ! preg_match("/^([a-z0-9])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric with underscores and dashes
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha_dash($str)
	{
		return ( ! preg_match("/^([-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function numeric($str)
	{
		return (bool)preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str);

	}

	// --------------------------------------------------------------------

	/**
	 * Is Numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function is_numeric($str)
	{
		return ( ! is_numeric($str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Integer
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function integer($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Decimal number
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function decimal($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Greather than
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function greater_than($str, $min)
	{
		if ( ! is_numeric($str))
		{
			return FALSE;
		}
		return $str > $min;
	}

	// --------------------------------------------------------------------

	/**
	 * Less than
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function less_than($str, $max)
	{
		if ( ! is_numeric($str))
		{
			return FALSE;
		}
		return $str < $max;
	}

	// --------------------------------------------------------------------

	/**
	 * Is a Natural number  (0,1,2,3, etc.)
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function is_natural($str)
	{
		return (bool) preg_match( '/^[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Is a Natural number, but not a zero  (1,2,3, etc.)
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function is_natural_no_zero($str)
	{
		if ( ! preg_match( '/^[0-9]+$/', $str))
		{
			return FALSE;
		}

		if ($str == 0)
		{
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Base64
	 *
	 * Tests a string for characters outside of the Base64 alphabet
	 * as defined by RFC 2045 http://www.faqs.org/rfcs/rfc2045
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function valid_base64($str)
	{
		return (bool) ! preg_match('/[^a-zA-Z0-9\/\+=]/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Prep data for form
	 *
	 * This function allows HTML to be safely shown in a form.
	 * Special characters are converted.
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function prep_for_form($data = '')
	{
		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				$data[$key] = $this->prep_for_form($val);
			}

			return $data;
		}

		if ($this->_safe_form_data == FALSE OR $data === '')
		{
			return $data;
		}

		return str_replace(array("'", '"', '<', '>'), array("&#39;", "&quot;", '&lt;', '&gt;'), stripslashes($data));
	}

	// --------------------------------------------------------------------

	/**
	 * Prep URL
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function prep_url($str = '')
	{
		if ($str == 'http://' OR $str == '')
		{
			return '';
		}

		if (substr($str, 0, 7) != 'http://' && substr($str, 0, 8) != 'https://')
		{
			$str = 'http://'.$str;
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Strip Image Tags
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function strip_image_tags($str)
	{
		return $this->HP->input->strip_image_tags($str);
	}

	// --------------------------------------------------------------------

	/**
	 * XSS Clean
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function xss_clean($str)
	{
		return $this->HP->security->xss_clean($str);
	}

	// --------------------------------------------------------------------

	/**
	 * Convert PHP tags to entities
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function encode_php_tags($str)
	{
		return str_replace(array('<?php', '<?PHP', '<?', '?>'),  array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $str);
	}

}
/* Location: ./libraries/Validation.php */