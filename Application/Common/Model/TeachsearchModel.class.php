<?php 

namespace Common\Model;
use Think\Model;

class TeachsearchModel extends Model{
	protected $tableName = 'teachsearch';

	public function getTeachsearch($gid){
		$sql = "SELECT a.name,a.num,a.finum,a.origin,a.begindate,a.enddate,a.permitfee,a.getfee,a.yearfee,a.yeardate,b.category as category,a.rank,a.score,c.optionaldate as submitdate,a.isfirstmoney 
		FROM teachsearch a,teachsearchcategory b,submitdate c
		where a.gid = '".$gid."'
		AND a.category = b.id AND a.submitdate = c.id";

		$data = $this -> query($sql);
		return $data;

	}
}