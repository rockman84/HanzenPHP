<?php
class HP_Output extends CI_Output{
public $HP;
	public function data($data = array()){
		$this->HP = & get_instance();
		if($this->HP->config->item('render_debug')){
			$data['bench'] = array(
				'speed'	=> $this->HP->benchmark->elapsed_time(),
				'memory'	=> $this->HP->benchmark->memory_usage()
			);
		}
		$this->HP->config->set_item('render_debug', false);
		$data['success'] = !$this->HP->msg->is_error;
		$data['msg'] = $this->HP->msg->get_all();
		$this->json($data);
	}
	// append to json data
	public function json($data){
		$this->set_content_type('application/json');
		$this->set_output(json_encode($data));
	}
	public function no_cache(){
		$this->set_header("Cache-Control: no-store, no-cache, must-revalidate");
		$this->set_header("Cache-Control: post-check=0, pre-check=0",false);
		$this->set_header("Pragma: no-cache");
		$this->set_header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	}
}
/* Location: ./core/HP_Output.php */