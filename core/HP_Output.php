<?php
class HP_Output extends CI_Output{
public $HP;
	// append to json data
	public function json($data){
		$this->set_content_type('json');
		$this->set_output(json_encode($data));
	}
	public function no_cache(){
		$this->set_header("Cache-Control: no-store, no-cache, must-revalidate")
			->set_header("Cache-Control: post-check=0, pre-check=0",false)
			->set_header("Pragma: no-cache")
			->set_header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		return $this;
	}
}
/* Location: ./core/HP_Output.php */