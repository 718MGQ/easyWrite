<?php  

namespace Common\Model;
use Think\Model;

class CompeteModel extends Model{

	protected $tableName = 'compete';

	public function getCompete($gid){
		$sql = "SELECT a.name,a.unit,a.permitdate,a.score,b.category as categoryid,c.optionaldate as submitdate_id
				FROM compete a,competecategory b,submitdate c
				WHERE a.gid = '".$gid."'
				AND a.categoryid = b.id AND a.submitdate_id = c.id";
		$data = $this -> query($sql);
		return $data;
	}
}