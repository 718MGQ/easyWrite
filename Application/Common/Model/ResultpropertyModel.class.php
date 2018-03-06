<?php 
	namespace Common\Model;
	use Think\Model;

	class ResultpropertyModel extends Model{
		protected $tableName = 'resultproperty';
		public function getProperty($gid){
			$sql = "SELECT a.name,a.unit,a.certificateid,a.permitdate,b.category as categoryid,a.score,c.optionaldate as submitdateid 
				FROM resultproperty a,resultpropertycategory b,submitdate c
			  	WHERE a.gid = '".$gid."'
			  	AND a.categoryid = b.id AND a.submitdateid = c.id";
			$data = $this ->query($sql);
			return $data;
		}
	}

