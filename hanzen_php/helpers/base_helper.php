<?php
/**
 * return path for root folder
 */
function root($path = ''){
	return base_url(config_item('webroot_folder').'/'.$path);
}
/**
 * quick call function with a single parameter
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
 **/
function get_plugin($class = null){
	$HP = & get_instance();
	if($class != null){
		if(!is_array($class)){
			$class = array($class);
		}
		foreach($class as $name){
			if(!isset($HP->plugin->$name)){
				$HP->load->plugin($name);
			}
		}
		if(count($class) == 1){
			$class = strtolower($class[0]);
			return $HP->plugin->$class;
		}
	}
	return $HP->plugin;
}
/**
 * return a model class
 **/
function get_model ($class = null){
	$HP = & get_instance();
	if($class != null){
		if(!is_array($class)){
			$class = array($class);
		}
		foreach($class as $name){
			if(!isset($HP->model->$name)){
				$HP->load->model($name);
			}
		}
		if(count($class) == 1){
			$class = strtolower($class[0]);
			return $HP->model->$class;
		}
	}
	return $HP->model;
}
/**
 * return a library class
 **/
function get_library($class = null){
	$HP = & get_instance();
	if($class != null){
		if(!is_array($class)){
			$class = array($class);
		}
		foreach($class as $name){
			$HP->load->library($name);
		}
		if(count($class) == 1){
			$class = strtolower($class[0]);
			return $HP->$class;
		}
	}
	return $HP;
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
 * quick set error message;
 */
function set_msg($string,$replace = null,$index = 'error'){
	$HZ = & get_instance();
	$HZ->msg->set($string,$replace,$index);
}