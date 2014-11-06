<?php
class User_agent_Plugin extends HP_Plugin {
public $device;
	public function __CONSTRUCT(){
		$this->load->library(array('user_agent','session'));
		$this->start();
	}
	public function start(){
		$view_type = $this->session->userdata('device');
		if($view_type == ''){
			$this->check_agent();
			$this->session->set_userdata(array('device'=>$this->device));
		}
	}
	private function check_agent (){
		if ($this->agent->is_robot()){
			$agent = $this->agent->robot();
			$type = 'bot';
		}
		elseif ($this->agent->is_mobile()){
			$agent = $this->agent->mobile();
			$type = 'mobile';
		}
		elseif ($this->agent->is_browser()){
			$agent = $this->agent->browser().' '.$this->agent->version();
			$type = 'browser';
		}
		else{
			$agent = 'unknown';
			$type = 'unknown';
		}
		$this->device = array(
			'agent' => $agent,
			'type' => $type,
			'view' => $type,
			'platform' => $this->agent->platform()
		);
	}
	public function destroy(){
		$this->session->set_userdata('device','');
	}
	public function get_device(){
		return $this->session->userdata('device');
	}
	public function toogle_device(){
		$data = $this->get_device();
		$new_view = 'bot';
		if($data['view'] == 'mobile'){
			$new_view = 'browser';
		}
		elseif($data['view'] == 'browser'){
			$new_view = 'mobile';
		}
		$data['view'] = $new_view;
		$this->session->set_userdata(array('device'=>$data));
	}
}
?>