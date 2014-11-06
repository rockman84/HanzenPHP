<?php
class Rest{
public $HP;
public $rtime = array();
protected $_allowed_methods = array(
	'post','get','delete','update'
);
protected $_supported_formats = array(
		'xml' => 'application/xml',
		'json' => 'application/json',
		'jsonp' => 'application/javascript',
		'serialized' => 'application/vnd.php.serialized',
		'php' => 'text/plain',
		'html' => 'text/html',
		'csv' => 'application/csv'
	);
	public function __CONSTRUCT(){
		$this->rtime['start'] = microtime(true);
		$this->HP = & get_instance();
	}
	public function __DESTRUCT(){
		$this->rtime['end'] = microtime(true);
		echo $this->rtime['end'] - $this->rtime['start'];
	}
	public function run(){
		echo $this->_detect_input_format();
		echo $this->_detect_method();
	}
	public function response($data = null, $status_code = null){
		$this->HP->output->set_content_type($this->_supported_formats['json']);
		
	}
	protected function _detect_ssl(){
		if($this->HP->config->item('rest_force_https')){
			return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on");
		}
		return true;
	}
	protected function _detect_method(){
		$method = strtolower($this->HP->input->server('REQUEST_METHOD'));
		if ($this->HP->config->item('enable_emulate_request')) {
			if ($this->HP->input->post('_method')) {
				$method = strtolower($this->HP->input->post('_method'));
			}
			elseif ($this->HP->input->server('HTTP_X_HTTP_METHOD_OVERRIDE')) {
				$method = strtolower($this->HP->input->server('HTTP_X_HTTP_METHOD_OVERRIDE'));
			}
		}
		if (in_array($method,$this->_allowed_methods) && method_exists($this,'_parse_'.$method)){
			return $method;
		}
		return null;
	}
	protected function _detect_input_format(){
		if ($this->HP->input->server('CONTENT_TYPE')) {
			// Check all formats against the HTTP_ACCEPT header
			foreach ($this->_supported_formats as $format => $mime) {
				if (strpos($match = $this->HP->input->server('CONTENT_TYPE'), ';')) {
					$match = current(explode(';', $match));
				}
				if ($match == $mime) {
					return $format;
				}
			}
		}
		return null;
	}
}
?>