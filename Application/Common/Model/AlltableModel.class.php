<?php 
namespace Common\Model;
use Think\Model;

class AlltableModel extends Model{

	protected $tableName = 'all_table';
	// 通过对应表的关键字获取对应的数据表的id,table_name
	public function getTableInfo($keyname){
		$sql = "SELECT id,table_name FROM all_table WHERE key_name = '".$keyname."'";
		$data = $this -> query($sql);
		return $data[0];
	}
}