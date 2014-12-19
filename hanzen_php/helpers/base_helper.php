<?php
/**
 * return path for webroot folder
 *
 * @param string
 * @return string
 */
function root($path = ''){
	return base_url(config_item('webroot_folder').'/'.$path);
}
/**
 * quick call function with a single parameter
 * split by "|" mark, Example : qcall('foo','htmlspecialchars|nl2br');
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
 * return a plug-in class
 * @example : get_plugin('plugin_name')->method_name();
 *
 * @param string / array
 * @return object
 **/
function get_plugin($plugin_name = null){
	$HP = & get_instance();
	if($plugin_name != null){
		if(!is_array($plugin_name)){
			$plugin_name = array($plugin_name);
		}
		foreach($plugin_name as $name){
			if(!isset($HP->plugin->$name)){
				$HP->load->plugin($name);
			}
		}
		if(count($plugin_name) == 1){
			$plugin_name = strtolower($plugin_name[0]);
			return $HP->plugin->$plugin_name;
		}
	}
	return $HP->plugin;
}
/**
 * return a model class
 * @example : get_model('model_name')->method_name();
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
 * @example : get_helper('helper_name');
 * 
 * @param string / array
 **/
function get_helper($helper_name){
	$HP = & get_instance();
	$HP->load->helper($helper_name);
}
/**
 * return a library class
 * @example : get_library('library_name')->method_name();
 *
 * @param string / array
 * @return object
 **/
function get_library($library_name = null){
	$HP = & get_instance();
	if($library_name != null){
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
function text($index, $replace = null){
	$HZ = & get_instance();
	$lang = $HZ->lang->line($index);
	if(is_array($replace)){
		$lang = $HZ->msg->replace($lang,$replace);
	}
	return $lang?$lang:'['.$index.']';
}
/**
 * quick set error message, this from library class Msg
 * same thing as $this->msg->set();
 * 
 * @param string
 * @param string
 * @param string
 * @return object (class Msg)
 */
function set_msg($string,$replace = null,$index = 'error'){
	$HZ = & get_instance();
	$HZ->msg->set($string,$replace,$index);
	return $HZ->msg;
}

/**
 * String replace
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