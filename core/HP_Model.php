<?php
class HP_Model extends CI_Model{
public $table;
public $rule;
protected $class;
private $tmp_tabel;

	public function __CONSTRUCT(){
		parent::__CONSTRUCT();
		$this->load->database();
		$this->class = str_replace('_model','',strtolower(get_class($this)));
		$this->table = $this->db->dbprefix($this->class);
		set_msg('Load Model: '.get_class($this),'','debug');
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
	/**
	* just return database class
	* note: this already automatic set table name
	*
	* @return database class
	**/
	public function db(){
		return $this->db->from($this->table);
	}
	/**
	 * same as insert table and with validation data
	 *
	 * @param array
	 **/
	public function create($data){
		if(get_library('validation')->check($data,$this->rule)){
			return $this->db->insert($this->class,$data);
		}
	}
	/**
	 * update table with validation data
	 *
	 * @param array
	 * @retrun database class
	 **/
	public function update($data){
		get_library('validation')->update = true;
		if($this->validation->check($data,$this->rule(),false)){
			$this->db->where($this->validation->primary,$data[$this->validation->primary]);
			unset($data[$this->validation->primary]);
			return $this->db->update($this->class,$data);
		}
	}
	/**
	* delete data from table
	*
	* @param string / array
	* @param number_format
	* @return database class
	**/
	public function delete($condition,$limit = false){
		if($limit){
			$this->db->limit($limit);
		}
		return $this->db->delete($this->table,$condition);
	}
	/**
	* get total row table
	*
	* @return number
	**/
	public function count_all(){
		return $this->db->count_all($this->table);
	}
}
/* Location: ./core/HP_Model.php */