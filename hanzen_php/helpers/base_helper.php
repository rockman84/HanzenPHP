<?php
/**
 * return path for webroot folder
 * example: [code] echo root('script.js');[/code]
 *
 * @param string
 * @return string
 */
function root($path = ''){
	return base_url(config_item('webroot_folder').'/'.$path);
}
/**
 * quick call function with a single parameter
 * split by "|" mark
 * Example : [code]qcall('foo','htmlspecialchars|nl2br');[/code]
 *
 * @param string
 * @param string - any function name with single parameter
 * @return mixed
 */
function qcall($raw,$function){
	foreach(explode('|',$function) as $role){
		if(substr($role,0,5)=='call_'){
			$role = substr($role,5);
			$raw = get_instance()->$role($raw);
		}
		else{
			$raw = $role($raw);
		}
	}
	return $raw;
}
/**
 * Load Class File
 *
 * @param string
 * @param string
 * @param string
 */
function &load_file($file,$prefix='',$surfix = ''){
	static $_files = array();
	$path = '';
	$name = FALSE;
	if (($last_slash = strrpos($file, '/')) !== FALSE){
		$path = substr($file, 0, $last_slash + 1);
		$file = substr($file, $last_slash + 1);
	}
	$class = ucfirst($prefix.$file.$surfix);
	if (isset($_files[$file])){
		return $_files[$file];
		
	}
	foreach(get_instance()->load->get_package_paths(FALSE)  as $val){
		$file_path = $val.$path.$file.'.php';
		if(file_exists($file_path)){
			$name = ucfirst($file);
			include_once $file_path;
			break;
		}
	}
	if($name === FALSE){
		show_error('File Not Found: '.$path.$file.'.php');
	}
	if(class_exists($class)){
		$_files[$file] = array(
			'name' => str_replace(array($prefix,$surfix),'',$file),
			'return' => new $class()
		);
	}
	return $_files[$file];
}
/**
 * return a plug-in class
 * @example : [code]get_plugin('plugin_name')->method_name();[/code]
 *
 * @param string | array
 * @return object
 **/
function get_plugin($plugin_name = null){
	$HP = & get_instance();
	return $HP->load->plugin($plugin_name);
}
/**
 * return a model class
 * @example : [code]get_model('model_name')->method_name();[/code]
 *
 * @param string / array
 * @param string
 * @param boolean
 * @return object
 **/
function get_model ($model_name = null,$name = '',$db_conn = False){
	$HP = & get_instance();
	if($model_name != null){
		if(!is_array($model_name)){
			$model_name = array($model_name);
		}
		$HP->load->model($model_name,$name,$db_conn);
		if(count($model_name) == 1){
			$model_name = strtolower($model_name[0]);
			if($name != ''){
				return $HP->model->$name;
			}
			return $HP->model->$model_name;
		}
	}
	return $HP->model;
}
/**
 * load helper
 * @example : [code]get_helper('helper_name');[/code]
 * 
 * @param string / array
 **/
function get_helper($helper_name){
	$HP = & get_instance();
	$HP->load->helper($helper_name);
}
/**
 * return a library class
 * @example : [code]get_library('library_name')->method_name();[/code]
 *
 * @param string / array
 * @return object
 **/
function get_library($library_name = null){
	$HP = & get_instance();
	if($library_name !== null){
		if(!is_array($library_name)){
			$library_name = array($library_name);
		}
		foreach($library_name as $name){
			$HP->load->library($name);
		}
		if(count($library_name) == 1){
			$library_name = strtolower($library_name[0]);
			return $HP->$library_name;
		}
	}
	return $HP;
}
/**
* load view
*
* @param string
* @param array
* @param boolean
* @return string/html
**/
function get_view($view_name = null,$data = array(),$return = TRUE){
	$HP = & get_instance();
	return $HP->load->view($view_name,$data,$return);
}
/**
* @params $index string, $replace array
* @return string
**/
function text($item, $replace = null){
	$HZ = & get_instance();
	$lang = $HZ->lang->line($item);
	if(is_array($replace)){
		$lang = string_replace($lang,$replace);
	}
	return $lang?$lang:'['.$item.']';
}
/**
 * quick set error message, this from library class Msg
 * same thing as [code]$this->msg->set();[/code]
 * 
 * @param string
 * @param array
 * @param string
 * @return object (class Msg)
 */
function set_msg($string,$replace = null,$group = 'error'){
	$HZ = & get_instance();
	$HZ->msg->set($string,$replace,$group);
	return $HZ->msg;
}

/**
 * String replace
 *
 * @param string
 * @param string | array
 */
function string_replace($string,$replace){
	if(is_array($replace)){
		foreach($replace as $name => $val){
			$word['find'][]= '{'.$name.'}';
			$word['replace'][] = $val;
		}
		return str_replace($word['find'],$word['replace'],$string);
	}
	else{
		return str_replace('{0}',$replace,$string);
	}
}