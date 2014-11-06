<?php
class HP_Input extends CI_Input{
public $get;
public $post;
	public function check($index,$type='get',$error = false){
		if(is_array($index)){
			foreach($index as $name => $val){
				if($type == 'post'){$this->post[$val] = $this->post($val);}
				else{$this->get[$val] = $this->get($val);}
			}
		}
	}
}
?>