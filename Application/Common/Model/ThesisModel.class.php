<?php 

namespace Common\Model;
use Think\Model;

class ThesisModel extends Model{
	protected $tableName = 'thesis';

	public function getTeachthesis($gid){
		$sql = "SELECT a.name,a.publisher,a.publishdate,a.rank,b.category as categoryid,a.score,c.optionaldate as submitdate,a.searchid,a.chapterno,a.startpage,a.endpage 
				FROM thesis a,thesiscategory b,submitdate c 
				where a.gid = '".$gid."' AND categoryid = 28
				AND a.categoryid = b.id AND a.submitdate = c.id";
		$data = $this->query($sql);
		return $data;
	}
	public function getSearchthesis($gid){
		$sql = "SELECT a.name,a.publisher,a.publishdate,a.rank,b.category as categoryid,a.score,c.optionaldate as submitdate,a.searchid,a.chapterno,a.startpage,a.endpage 
				FROM thesis a,thesiscategory b,submitdate c 
				where a.gid = '".$gid."' AND categoryid <> 28
				AND a.categoryid = b.id AND a.submitdate = c.id";
		$data = $this->query($sql);
		return $data;
	}
}