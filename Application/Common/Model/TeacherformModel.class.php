<?php  

namespace Common\Model;
use Think\Model;

class TeacherformModel extends Model{
	protected $tableName = 'teacherform';
	public function getTeacherform($gid){

		$sql = "SELECT a.name,a.num,a.finum,a.origin,a.begindate,a.enddate,a.permitfee,a.getfee,a.yearfee,a.yeardate,a.rank,b.category as categoryid,a.score,c.optionaldate as submitdate,a.isfirstmoney 	 FROM teacherform a,teacherformcategory b,submitdate c 
				where a.gid = '".$gid."'
				AND a.categoryid = b.id AND a.submitdate = c.id";
		$data = $this->query($sql);
		return $data;
	}
}