<?php

function root($path = ''){
	$HZ =& get_instance();
	return base_url($HZ->config->item('webroot_folder').'/'.$path);
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
function set_msg($string,$replace = null,$index = 'error'){
	$HZ = & get_instance();
	$HZ->msg->set($string,$replace,$index);
}
function flash_msg($item = 'messages'){
	$HZ = & get_instance();
	$HZ->session->set_flashdata($item,$HZ->msg->get_all());
}
function get_date($time,$time_fmt = false){
	$HZ = & get_instance();
	$fmt = $time_fmt?$HZ->config->item('date_fmt').' '.$HZ->config->item('time_fmt'):$HZ->config->item('date_fmt');
	return date($fmt,$time);
}
function get_post_json($toIndex = false,$root='data'){
	$data = json_decode(file_get_contents('php://input'), true);
	if($toIndex){
		$data = array2index($data[$root]);
	}
	else{
		$data = $data[$root];
	}
	return $data;
}
function array2index($data){
	if(!isset($data[0])){
		$tmp[] = $data;
		$data = $tmp;
		unset($tmp);
	}
	return $data;
}