<?php 
namespace Home\Controller;
use Think\Controller;

class ConfirmController extends Controller
{
	/*
	unchecked:待审批
	unconfirm:待修改
	unwrited:未填写
	finished:完成
	0 => 待填写
	1 => 待修改
	2 => 待审批
	3 => 已完成
	 */

	public function index()
	{
		
		$step = array(
					0 => 'unwrited',
					1 => 'unconfirm',
					2 => 'unchecked',
					3 => 'finished'
			);
		$gid = session('gid');
		//$gid = 130001;
		$status = D('Taskstatus');
		$data = $status->get_data($gid);

		// echo '<pre>';
		// print_r($data);exit;
		$task_arr = array();
		if(count($data) != 0)
		{
			$task_arr[0]['file_name'] = $data[0]['file_name'];
			$task_arr[0]['task_id']   = $data[0]['id'];
			$task_arr[0]['task_time'] = date('Y-m-d H:i:s',$data[0]['task_time']);
			//$task_arr[0]['now_step'] = null;

			$unwrite = array(
							'times'     => 0,
							'timestamp' => array()
				);
			$unconfirm = array(
							'times'     => 0,
							'timestamp' => array()
				);
			$unchecked = array(
							'times'     => 0,
							'timestamp' => array()
				);
			$finished = array(
							'times'     => 0, 
							'timestamp' => array()
				);
			for($i = 0, $j=0; $i < count($data); $i++)
			{
				if($data[$i]['id'] === $task_arr[$j]['task_id'])
				{
					$task_arr[$j]['now_step'] = $step[$data[$i]['status']];
					switch ($data[$i]['status']) {
						case 0:
							$unwrite['times']++;
							$unwrite['timestamp'][] = $data[$i]['timestamp'];
							break;
						case 1:
							$unconfirm['times']++;
							$unconfirm['timestamp'][] = $data[$i]['timestamp'];
							break;
						case 2:
							$unchecked['times']++;
							$unchecked['timestamp'][] = $data[$i]['timestamp'];
							break;
						case 3:
							$finished['times']++;
							$finished['timestamp'][] = $data[$i]['timestamp'];
							break;
					}
				}
				else
				{
					$j++;
					$task_arr[$j]['file_name'] = $data[$i]['file_name'];
					$task_arr[$j]['task_id']   = $data[$i]['id'];
					$task_arr[$j]['task_time'] = date('Y-m-d H:i:s',$data[$i]['task_time']);
					$unwrite['times'] = 0;
					$unwrite['timestamp'] = array();
					$unconfirm['times'] = 0;
					$unconfirm['timestamp'] = array();
					$unchecked['times'] = 0;
					$unchecked['timestamp'] = array();
					switch ($data[$i]['status']) {
						case 0:
							$unwrite['times']++;
							$unwrite['timestamp'][] = $data[$i]['timestamp'];
							break;
						case 1:
							$unconfirm['times']++;
							$unconfirm['timestamp'][] = $data[$i]['timestamp'];
							break;
						case 2:
							$unchecked['times']++;
							$unchecked['timestamp'][] = $data[$i]['timestamp'];
							break;
						case 3:
							$finished['times']++;
							$finished['timestamp'][] = $data[$i]['timestamp'];
							break;
					}
				}
				$task_arr[$j]['unwrite'] = $unwrite;
				$task_arr[$j]['unconfirm'] = $unconfirm;
				$task_arr[$j]['unchecked'] = $unchecked;
				$task_arr[$j]['finished'] = $finished;
			}

			for($i = 0;$i < count($task_arr);$i++){
				if($task_arr[$i]['now_step'] == 'unwrited'){
					$task_arr[$i]['unwrite']['timestamp'][] = '0';
				}
				elseif($task_arr[$i]['now_step'] == 'unconfirm')
				{
					$task_arr[$i]['unconfirm']['timestamp'][] = '0';
				}
				elseif($task_arr[$i]['now_step'] == 'unchecked')
				{
					$task_arr[$i]['unchecked']['timestamp'][] = '0';
				}elseif($task_arr[$i]['now_step'] != 'finished'){
					$task_arr[$i]['finished']['timestamp'][] = '0';
				}
			}
		}
		$da = array(
					'headimgurl' => session('headimgurl'),
					'num'        => $gid,
					'host'		=> "",
					'task_arr'   => $task_arr

			);


		//echo json_decode($da);
		//print_r($da);exit;
		$this->ajaxReturn($da);
	}

	/**
	 * 文件汇总界面
	 * @return 
	 */
	          
	public function allfile()
	{
		//$date = I('get.date', null);
		//echo $date;die;
		$date = date("Y-m", time());
		//$user = session('user');
		$user = session('gid');
		//$user = 130003;
		// if($date === null || $user === null)
		// {
		// 	$this->ajaxReturn(false);
		// }
		$task = D('Task');
		//获取所有已完成文件
		$datas =$task->get_datas($user, $date);
		$finished = array();
		$con = array(); 
		$sum = 0;
		for($i =0; ; $i++)
		{
			$pre_date = date('Y-m',strtotime($date) - $i*30*86400);
			for($j = 0, $len = count($datas); $j < $len; $j++)
			{
				$time = date('Y-m',$datas[$j]['submit_time']);
				if($time >= $pre_date)
				{
					$sum++;
					$con['name']        = $datas[$j]['file_name'];
					$con['task_time']   = 0;
					$con['id']          = $datas[$j]['id'];
					$con['finish_time'] = date('Y-m-d H:i:s',$datas[$j]['submit_time']);
					$finished[$time][] = $con; 
				}
			}
			// echo "<pre>";
			// print_r($finished);
			// echo "</pre>";
			if($sum >= count($datas))
			{
				break;
			}
			if(count($con) >= 10)
			{
				 
				break;
			}
			else
			{
				continue;
			}
		}
		//获取未填写文件
		$writed = $task->get_writed($user); 
		$unwrite = array();
		$sum = 0;
		for($i =0; ; $i++)
		{
			$pre_date = date('Y-m',time() - $i*30*86400);
			for($j = 0, $len = count($writed); $j < $len; $j++)
			{
				$time = date('Y-m',$writed[$j]['task_time']);
				if($time >= $pre_date)
				{
					$sum++;
					$con['name']        = $writed[$j]['file_name'];
					$con['task_time']   = date('Y-m-d H:i:s',$writed[$j]['task_time']);
					$con['id']          = $writed[$j]['id'];
					$con['finish_time'] = 0;
					$unwrite[$time][] = $con;
				}
			}
			// echo "<pre>";
			// print_r($unwrite);
			// echo "</pre>";
			if($sum >= count($writed))
			{
				break;
			}
		}
		//获取待审批
		$check = $task->get_sp($user); 
		$uncheck = array();
		$sum = 0;
		for($i =0; ; $i++)
		{
			$pre_date = date('Y-m',time() - $i*30*86400);
			for($j = 0, $len = count($check); $j < $len; $j++)
			{
				$time = date('Y-m',$check[$j]['task_time']);
				if($time >= $pre_date)
				{
					$sum++;
					$con['name']        = $check[$j]['file_name'];
					$con['task_time']   = date('Y-m-d H:i:s',$check[$j]['task_time']);
					$con['id']          = $check[$j]['id'];
					$con['finish_time'] = 0;
					$uncheck[$time][] = $con;
				}
			}
			if($sum >= count($check))
			{
				break;
			}
		}
		//待修改
		$confirm = $task->get_confirm($user); 
		$unconfirm = array();
		$sum = 0;
		for($i =0;  ; $i++)
		{
			$pre_date = date('Y-m',time() - $i*30*86400);
			for($j = 0, $len = count($confirm); $j < $len; $j++)
			{
				$time = date('Y-m',$confirm[$j]['task_time']);
				if($time >= $pre_date)
				{
					$sum++;
					$con['name']        = $confirm[$j]['file_name'];
					$con['task_time']   = date('Y-m-d H:i:s',$confirm[$j]['task_time']);
					$con['id']          = $confirm[$j]['id'];
					$con['finish_time'] = 0;
					$unconfirm[$time][] = $con;
				}
			}
			// echo "<pre>";
			// print_r($unconfirm);
			// echo "</pre>";
			if($sum >= count($confirm))
			{
				break;
			}
		}
		$data = array(
						'finished'  => $finished,
						'unwrite'   => $unwrite,
						'unconfirm' => $unconfirm,
						'unchecked' => $uncheck
				);
			 //echo '<pre>';
			 //print_r($data);exit;
			$this->ajaxReturn($data,'JSON');

	}

	// public function p()
	// {
	// 	header("Location:/index.php/Home/User/dis");
	// }
 }


 



