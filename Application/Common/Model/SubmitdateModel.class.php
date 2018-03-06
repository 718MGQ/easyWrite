<?php 

namespace Common\Model;
use Think\Model;

class SubmitdateModel extends Model{
	protected $tableName = 'submitdate';
	public function get_submitdata(){
		$sql = "SELECT * from '".$tableName."'";
		$data = $this->query($sql);
		return $data;
	}


}