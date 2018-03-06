<?php 
namespace Common\Model;
use Think\Model;

class TaskModel extends Model
{
	protected $tableName = 'Task';

	//返回task表中三个字段
	public function get_task()
	{
		$data = $this->field('id,file_name,task_time')
					 ->select();
		return $data;
	}
	//通过任务名返回文件名
	public function get_name($id)
	{
		$data = $this->field('file_name')
					 ->where('id = %d',$id)
					 ->find();
		return $data['file_name'];
	}
	//返回填表截止时间
	public function get_end_time($id)
	{
		$data = $this->field('task_time')
					 ->where('id = %d',$id)
					 ->find();
		return $data['task_time'];
	}

	//返回已完成文件
	function get_datas($user, $date)
	{
		$date_limit = strtotime(date('Y-m-30 00:00:00', strtotime($date)));
		$sql = '
			SELECT task.file_name, task_info.submit_time, task.id
			FROM task, (
				SELECT task_id, submit_time
				FROM task_info
				WHERE gid = %d AND submit_time < %d AND writed = 0 AND isupdata = 0 AND is_sp = 0
			) task_info
			WHERE task.id = task_info.task_id 
			ORDER BY task_info.submit_time desc
		';
		$datas = $this->query($sql, $user, $date_limit);
		return $datas;
	}
	//获取待填写文件
	function get_writed($user)
	{
		$sql = '
			SELECT task.file_name, task.task_time, task.id
			FROM task, (
				SELECT task_id
				FROM task_info
				WHERE gid = %d AND writed = 1 AND isupdata = 0 AND is_sp = 0
			) task_info
			WHERE task.id = task_info.task_id
			ORDER BY task.task_time desc
		';
		$datas = $this->query($sql, $user);
		return $datas;
	}
	//获取待修改文件
	function get_confirm($user)
	{
		$sql = '
			SELECT task.file_name, task.task_time, task.id
			FROM task, (
				SELECT task_id
				FROM task_info
				WHERE gid = %d AND writed = 0 AND isupdata = 1 AND is_sp = 0
			) task_info
			WHERE task.id = task_info.task_id
			ORDER BY task.task_time desc
		';
		$datas = $this->query($sql, $user);
		return $datas;
	}
	//获取待审批文件
	function get_sp($user)
	{
		$sql = '
			SELECT task.file_name, task.task_time, task.id
			FROM task, (
				SELECT task_id
				FROM task_info
				WHERE gid = %d AND writed = 0 AND isupdata = 0 AND is_sp = 1
			) task_info
			WHERE task.id = task_info.task_id
			ORDER BY task.task_time desc
		';
		$datas = $this->query($sql, $user);
		return $datas;
	}


	public function getStatus($user,$taskid)
	{
		$status = array(
			'待填写' => 0,
			'带修改' => 1,
			'待审批' => 2,
			'已完成' => 3
			);
		$sql = 'SELECT writed,isupdata,is_sp from task_info where gid = '.$user.' AND '.'task_id = '.$taskid;
		$data = $this -> query($sql);
		if($data['writed'] == 0 && $data['isupdata'] == 0 && $data['is_up'] == 0){
			return $status['已完成'];
		}elseif ($data['writed'] == 1 && $data['isupdata'] == 0 && $data['is_up'] == 0) {
			return $status['待填写'];
		}elseif ($data['writed'] == 0 && $data['isupdata'] == 1 && $data['is_up'] == 0) {
			return $status['带修改'];
		}elseif ($data['writed'] == 0 && $data['isupdata'] == 0 && $data['is_up'] == 1) {
			return $status['待审批'];	
		}
	}
	//通过gid返回文件名
	public function get_file_name($gid)
	{
		$sql = 'SELECT file_name name from task where upfile_gid = %s';
		$data = $this->query($sql, $gid);
		return $data;
	}

}




