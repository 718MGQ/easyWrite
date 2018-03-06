<?php 
	namespace PC\Controller;
	use Think\Controller;
class ShowFileInfoController extends Controller{
	/**
	 * 点击表名显示的表的数据内容
	 * @return [type] [description]
	 */
	public function tableData(){
		$taskId = I('get.id');
		// $taskId = 3;
		$user = session('gid');
		// $user = 130001;
		if(empty($user)){
			$res = array(
				'code' => 2,
				'message' => '用户未登录',
				'data' => array()
				);
			$this -> ajaxReturn($res);
		}
		//$taskId = 3;
		$task = D('Task');
		$status = $task -> getStatus($user,$taskId);
		$filename = $task -> get_name($taskId);
		$filename.='.xlsx';
		if(empty($filename)){
			$res = array(
				'code' => 1,
				'message' => '文件未找到',
				'data' => array()
				);
			$this -> ajaxReturn($res);
		}
		$reader = A('ReaderFile');

		$data = $reader -> getFinalData($user,$filename);
		$columns = $data['0'];
		$result = $data['1'];
		$column = array();
		for($i = 0;$i < count($result[0]);$i++){
			if($result[0][$i] == '序号'){
				$column[$i]['title'] = $result[0][$i];
				$column[$i]['dataIndex'] = 'index';
			}else{
				$column[$i]['title'] = $result[0][$i];
				$column[$i]['dataIndex'] = $columns[$i-1][0];
			}
		}
		$datasource = array();
		for($i = 1;$i < count($result);$i++){
			for($j = 0;$j < count($result[$i]);$j++){
				$t = $column[$j]['dataIndex'];
				$datasource[$i-1][$t] = $result[$i][$j];
			}
		}
		$form = array(
			'fileName' => $filename,
			'version' => time(),
			'status' => $status,
			'columns' => $column,
			'dataSource' => $datasource
			);
		
		$res = array(
			'code' => 0,
			'message' => '',
			'data' => $form
			);
		$this -> ajaxReturn($res,'json');
		//echo json_encode($res);die;
	}
	/**
	 * 文档处理页面，收前端回传的taskid及文档array，对比数据库中的信息
	 * 提取有效信息，写入数据库。
	 * 删除原有数据，把接收的array写入
	 * @return [type] [description]
	 */
	public function checkFileContent(){


// $json = '{
// 	"code":0,
// 	"message":"",
// 	"data":{
// 	"fileName":"\u4e13\u5229\u4fe1\u606f\u8c03\u67e5\u8868.xlsx",
// 	"version": 1508500878,
// 	"status":3,
// 	"columns":[
// 		{"title":"\u5e8f\u53f7","dataIndex":"index"},
// 		{"title":"\u9519\u8bef1","dataIndex":"temp1"},
// 		{"title":"\u6279\u51c6\u5355\u4f4d","dataIndex":"unit"},
// 		{"title":"\u9519\u8bef2","dataIndex":"temp3"},
// 		{"title":"\u8bc1\u4e66\u7f16\u53f7","dataIndex":"certificateid"},
// 		{"title":"\u6279\u51c6\u65f6\u95f4","dataIndex":"permitdate"},
// 		{"title":"\u7c7b\u522b","dataIndex":"categoryid"},
// 		{"title":"\u5f97\u5206","dataIndex":"score"}
// 	],
// 	"dataSource":[
// 		{
// 			"index":1,
// 			"temp1":"暂住证",
// 			"unit":"\u4e1c\u5317\u6797\u4e1a\u5927\u5b66",
// 			"temp3":"暂住证",
// 			"certificateid":"1000036X45",
// 			"permitdate":"20151212",
// 			"categoryid":"国家自然科学技术发明一等奖",
// 			"score":"1"
// 		},
// 		{
// 			"index":2,
// 			"temp1":"暂住证",
// 			"unit":"\u4e1c\u5317\u6797\u4e1a\u5927\u5b66",
// 			"temp3":"",
// 			"certificateid":"122233652x",
// 			"permitdate":"20110425",
// 			"categoryid":"国家自然科学技术发明二等奖",
// 			"score":"2"
// 		},
// 		{
// 			"index":3,
// 			"temp1":"暂住证",
// 			"unit":"待修改1",
// 			"temp3":"待修改2",
// 			"certificateid":"123565464sad",
// 			"permitdate":"1234214",
// 			"categoryid":"国家自然科学技术发明一等奖",
// 			"score":"2"
// 		},
// 		{
// 			"index":4,
// 			"temp1":"暂住证",
// 			"unit":"暂住证",
// 			"temp3":"暂住证",
// 			"certificateid":"edwqedqw",
// 			"permitdate":"6456544",
// 			"categoryid":"国家自然科学技术发明一等奖",
// 			"score":"2"
// 		},
// 		{"index":5,"temp1":null,"unit":null,"temp3":null,"certificateid":null,"permitdate":null,"categoryid":null,"score":null},{"index":6,"temp1":null,"unit":null,"temp3":null,"certificateid":null,"permitdate":null,"categoryid":null,"score":null},{"index":7,"temp1":null,"unit":null,"temp3":null,"certificateid":null,"permitdate":null,"categoryid":null,"score":null},{"index":8,"temp1":null,"unit":null,"temp3":null,"certificateid":null,"permitdate":null,"categoryid":null,"score":null},{"index":9,"temp1":null,"unit":null,"temp3":null,"certificateid":null,"permitdate":null,"categoryid":null,"score":null},{"index":10,"temp1":null,"unit":null,"temp3":null,"certificateid":null,"permitdate":null,"categoryid":null,"score":null},{"index":11,"temp1":null,"unit":null,"temp3":null,"certificateid":null,"permitdate":null,"categoryid":null,"score":null},{"index":12,"temp1":null,"unit":null,"temp3":null,"certificateid":null,"permitdate":null,"categoryid":null,"score":null},{"index":13,"temp1":null,"unit":null,"temp3":null,"certificateid":null,"permitdate":null,"categoryid":null,"score":null},{"index":14,"temp1":null,"unit":null,"temp3":null,"certificateid":null,"permitdate":null,"categoryid":null,"score":null}]}
// 	}';

		$gid = session('gid');
		if(empty($gid)){
			$this -> ajaxReturn(array(
				'code'=>1,
				'message'=>'用户已失效',
				'data'=>array()
				));
		}
		$data = json_decode(key($_REQUEST), true);
		if(empty($data)){
			$thid -> ajaxReturn(array(
				'code'=>1,
				'message'=>'数据拉取失败',
				'data'=>array()
				));
		}

		// $gid = '130001';
		$username = D('Teacher') -> getNameBygid($gid);
		//die($username);
		// $taskId = I('post.');
		//获取到前台传的更新过的表content.
		$columns = $string['data']['columns'];
		$result = $string['data']['dataSource'];
		//更新后的内容
		$taskId = $data['id'];	//任务ID



		//通过前端传的任务id获取对应excel的name.
		$task = D('Task');
		$filename = $task -> get_name($taskId);
		$filename.='.xlsx';
		
		//返回数据库中的原数据		
		$reader = A('ReaderFile');
		$data = $reader -> getFinalData($gid,$filename);
		$columnstandrad = $data['0'];
		$resultstandrad = $data['1'];//数据库中的原内容
		$count = $data['count'];//数据库中的数据记录条数
		$tablename = $data['2'];	//对应数据表
		$dataSource = $this->restore_array($result);
		$dataSource[0][2]='待更新';
		// echo $count;die;
		// print_r($columnstandrad);
		// echo '$dataSource';
		// print_r($dataSource);die;
		// echo 'resultstandrad';
		// print_r($resultstandrad);die;
		// echo '$columnstandrad';
		// print_r($columnstandrad);die;
		// print_r($columns);print_r($dataSource);print_r($data);die;
		$sqlInsert = 'INSERT INTO '.$tablename.' (gid,';
		$va = $gid.',';
		$deletesql = 'DELETE FROM '.$tablename.' WHERE ';
		$a = 0;
		$dataSourceCount = count($dataSource);
		for($i = 0;$i < $dataSourceCount ;$i++)
		{
			if($i < $count)
			{//表示这i行数据被修改(updata);
				for ($j=1 ,$dataSicount = count($dataSource[$i]); $j < $dataSicount; $j++) 
				{
					if($j < $dataSicount -1)
					{
						$deletesql .= $columnstandrad[$j-1][0].' = '.$this->getContentOrNull($resultstandrad[$i+1][$j]).' AND ';			
					}
					else
					{
						$deletesql .= $columnstandrad[$j-1][0].' = '.$this->getContentOrNull($resultstandrad[$i+1][$j]);
					}
				}
				$sql[$a++] = $deletesql;
				$deletesql = 'DELETE FROM '.$tablename.' WHERE ';
				//echo $i."<br>";
			}
		}
		for($i = 0;$i < $dataSourceCount ;$i++)
		{//剩余行数未填写或全部填写（INSERT）;
			{
				if($this->allLineIsNull($dataSource[$i]) == false)
				{
					for ($j=1 ,$dataSicount = count($dataSource[$i]); $j < $dataSicount; $j++) 
					{

							if($j != $dataSicount -1) 
								{
									if($this->checkStr($columnstandrad[$j-1][0],'category') && $dataSource[$i][$j] != null)
									{
										$sqlInsert .= $columnstandrad[$j-1][0].',';
										$va .= $this->getIdByCategory($dataSource[$i][$j]).',';
									}
									else
									{
										$sqlInsert .= $columnstandrad[$j-1][0].',';						
										$va .= $this->getContentOrNull($dataSource[$i][$j]).',';
									}
								}
							else
								{
									if($this->checkStr($columnstandrad[$j-1][0],'category') && $dataSource[$i][$j] != null)
									{
										$sqlInsert .= $columnstandrad[$j-1][0].')';
										$va .= $this->getIdByCategory($dataSource[$i][$j]);
									}
									else
									{
										$sqlInsert .= $columnstandrad[$j-1][0].')';
										$va .= $this->getContentOrNull($dataSource[$i][$j]);
									}
								}
						
					}
				    $sql[$a++] = $sqlInsert.' VALUES '.'('.$va.')';
					$sqlInsert = 'INSERT INTO '.$tablename.' (gid,';
					$va = $gid.',';
				}
			}
		}
		$sqlAll = '';
		for($i = 0;$i < count($sql);$i++){
			$sqlAll = $sqlAll.$sql[$i].';';
		// die($sqlAll);
		// echo $sqlAll;
		}
		if(M($tablename) -> execute($sqlAll))
		{
			$this->ajaxReturn(array(
					'code' => 0,
					'message' => 'It work',
					'data' => array()
					));
		}
	}
	/**
	 * 索引数字化
	 * @param  [type] $array [description]
	 * @return [type] $array [description]
	 */
	public function restore_array($array){
		$arr = array();
		$i = 0;$j = 0;
		foreach ($array as $key => $value) {
			foreach($array[$key] as $k => $va){
				$arr[$i][$j] = $va;
				$j++;
			}
			$i++;
			$j = 0;
		}
		return $arr;
	}
	/**
	 * 若全为空，返回true.否则返回false;
	 * @param  $array $dataSource 
	 * @return bool    
	 */
	public function allLineIsNull($dataSource){
		$count = count($dataSource);
		for($i = 1;$i < $count;$i++)
		{
			if(!empty($dataSource[$i]))
			{
				return false;
			}
		}
		return true;
	}
	/**
	 * 判断是否为字符串且返回为带单引号的字符串
	 * @param  [type] $a [description]
	 * @return [type]    [description]
	 */
	function getContentOrNull($a){
		if(is_string($a)){
			$a = '\''.$a.'\'';
			return $a != null ? $a : '';
		}elseif(is_numeric($a))
		{
			return $a != null ? $a : 'null';
		}elseif($a == null){
			return 'null';
		}
	}
	/**
	 * 针对类别项，找到id
	 * @param  [type] $category [description]
	 * @return [type]           [description]
	 */
	public function getIdByCategory($category){
		$id = D('Allcolumns')->getId($category);
		return $id; 
	}
	/**
	 * 检查一个字符串中是否包含另一个字符串
	 * @param  [type] $string [description]
	 * @param  [type] $target [description]
	 * @return [type]         [description]
	 */
	public function checkStr($string,$target){
	  $tmp = strpos($string,$target);
	  //print_r($tmpArr);
	  if($tmp !== false)
	  	return true;
	  else 
	  	return false;
	}
	public function test(){
		$str = '国家自然科学技术发明二等奖';
		
		if($this -> checkStr('categoryid','category'))
		{
			$id = $this->getIdByCategory($str);
		}
		echo $id;
	}
}
