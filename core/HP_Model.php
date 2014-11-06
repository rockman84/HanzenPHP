<?php
class HP_Model extends CI_Model{
public $HZ;
public $table;
protected $class;
private $tmp_tabel;

	public function __CONSTRUCT(){
		parent::__CONSTRUCT();
		$this->load->database();
		$this->class = str_replace('_model','',strtolower(get_class($this)));
		$this->table = $this->db->dbprefix($this->class);
	}
	/** remaping table and rules **/
	protected function set_table($value,$index = null){
		$table = '';
		if(is_array($value)){
			foreach($value as $n => $v){
				if(is_array($v)){
					$table[$n] = array('name'=>$this->db->dbprefix($n),'rule'=>$v);
				}
				else{
					$table = $this->set_table($v,$n);
				}
			}
		}
		else{
			$table[$value] = array('name'=>$this->db->dbprefix($value),'rule'=>array());
			unset($table[$index]);
		}
		return $table;
	}
	public function db(){
		return $this->db->from($this->table);
	}
	public function create($data){
		$this->HZ->load->library('validation');
		if($this->validation->check($data,$this->rule())){
			return $this->HZ->db->insert($this->class,$data);
		}
	}
	public function update($data){
		$this->load->library('validation');
		$this->validation->update = true;
		if($this->validation->check($data,$this->rule(),false)){
			$this->HZ->db->where($this->validation->primary,$data[$this->validation->primary]);
			unset($data[$this->validation->primary]);
			return $this->HZ->db->update($this->class,$data);
		}
	}
	public function delete($condition,$limit = false){
		if($limit){
			$this->HZ->db->limit($limit);
		}
		return $this->HZ->db->delete($this->table,$condition);
	}
	public function count_all(){
		return $this->HZ->db->count_all($this->table);
	}
}
/* Location: ./core/HP_Model.php */