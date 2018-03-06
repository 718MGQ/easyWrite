<?php 
namespace Common\Model;
use Think\Model;

class InnovationteamModel extends Model{
	protected $tableName = 'innovationteam';

	public function getInnovationteam($gid){
		$sql = "SELECT a.name,a.unit,a.permitdate,b.category as categoryid,a.score,c.optionaldate as submitdate 
		FROM innovationteam a,innovationteamcategory b,submitdate c
		where a.gid = '".$gid."'
		AND a.categoryid = b.id AND a.submitdate = c.id";

		$data = $this -> query($sql);
		return $data;

	}
}