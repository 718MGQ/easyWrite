<?php 

namespace Common\Model;
use Think\Model;

class TeachbookModel extends Model{

	protected $tableName = 'teachbook';

	public function getTeachmaterial($gid){
		$sql = "SELECT a.name,a.num,a.publisher,a.publishdate,a.count,b.category as categoryid,a.score,
					c.optionaldate as submitdate
				from teachbook a,teachbookcategory b,submitdate c
				where a.gid = '".$gid."' AND (a.categoryid = 5 OR a.categoryid = 6 OR a.categoryid = 7)
				AND a.categoryid = b.id AND a.submitdate = c.id";
		$data = $this -> query($sql);
		return $data;
	}
	public function getMonograph($gid){
		$sql = "SELECT a.name,a.num,a.publisher,a.publishdate,a.count,b.category as categoryid,a.score,
					c.optionaldate as submitdate
				from teachbook a,teachbookcategory b,submitdate c
				where a.gid = '".$gid."' AND (a.categoryid = 1 OR a.categoryid = 2 OR a.categoryid = 3 OR a.categoryid = 4) 
				AND a.categoryid = b.id AND a.submitdate = c.id";
		$data = $this -> query($sql);
		return $data;
	}
}