<?php 

namespace Common\Model;
use Think\Model;

class TalentModel extends Model{
	protected $tableName = 'talent';

	public function getTalent($gid){
		$sql = "SELECT a.name,a.unit,a.permitdate,b.category as categoryid,a.score,c.optionaldate as submitdate
				from talent a,talentcategory b,submitdate c
				where a.gid = '".$gid."'
				AND a.categoryid = b.id AND a.submitdate = c.id";
		$data = $this -> query($sql);
		return $data;
	}
}