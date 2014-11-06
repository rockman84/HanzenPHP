<?php
class HP_Lang extends CI_Lang {
public $set_lang;
public $keep_lang = array();
	public function __CONSTRUCT(){
		parent::__CONSTRUCT();
	}
	public function open($file,$keep = false){
		$path = APPPATH.'language/'.$this->set_lang.'/'.$file.'_lang'.EXT;
		$lang = '';
		if(file_exists($path)){
			$lang = $this->load($file,$this->set_lang,$keep);
		}
		else{
			if(file_exists(APPPATH.'language/english/'.$file.'_lang'.EXT)){
				$lang = $this->load($file,'english',$keep);
			}
		}
		if($keep){
			return $this->keep_lang = $lang;
		}
	}
}
?>