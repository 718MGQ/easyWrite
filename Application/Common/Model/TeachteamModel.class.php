<?php 
	namespace Common\Model;
	use Think\Model;
/**
 * 教学团队/教师教学奖/指导优秀学位论文
 */
class TeachteamModel extends Model{
	protected $tableName = 'teachteam';
//指导优秀学位论文
	public function getTeachthesis($gid){
		$sql = "SELECT a.name,a.unit,a.permitdate,b.category as categoryid,a.score,c.optionaldate as submitdate 
				FROM teachteam a,teachteamcategory b,submitdate c 
				where a.gid = '".$gid."' AND (categoryid = 4 OR categoryid = 5 categoryid = 9 OR categoryid = 10 OR categoryid = 11 OR categoryid = 12 OR categoryid = 13 OR categoryid = 14)
				AND a.categoryid = b.id AND a.submitdate = c.id";
		$data = $this ->query($sql);
		return $data;
	}
//教学团队
	public function getTeachteam($gid){
		$sql = "SELECT a.name,a.unit,a.permitdate,b.category as categoryid,a.score,c.optionaldate as submitdate 
				FROM teachteam a,teachteamcategory b,submitdate c 
				where a.gid = '".$gid."' AND (categoryid = 1 OR categoryid = 2 OR categoryid = 3)
				AND a.categoryid = b.id AND a.submitdate = c.id";
		$data = $this ->query($sql);
		return $data;
	}
//教师教学奖
	public function getTeachaword($gid){
		$sql = "SELECT a.name,a.unit,a.permitdate,b.category as categoryid,a.score,c.optionaldate as submitdate 
				FROM teachteam a,teachteamcategory b,submitdate c 
				where a.gid = '".$gid."' AND (categoryid = 6 OR categoryid = 7 OR categoryid = 8)
				AND a.categoryid = b.id AND a.submitdate = c.id";
		$data = $this ->query($sql);
		return $data;
	}
}