<?php 
namespace Common\Model;
use Think\Model;

class TaskinfoModel extends Model
{
	protected $tableName = 'task_info';
	public function get_taskinfo($gid)
	{
		$data = $this->where('gid = %d',$gid)
					 ->select();
		return $data;
	}
	/*
	返回PC端数据
	 */
	public function get_pc($gid)
	{
		$sql = '
				SELECT task.file_name,task_info.task_id,task_info.submit_time,task.task_time,task_info.writed,task_info.isupdata,task_info.is_sp 
				FROM task,task_info
				WHERE task_info.task_id=task.id and task_info.gid=%d
		';
		$data = $this->query($sql,$gid);
		return $data;
	}
	//返回taskinfo表中"finish"字段（审核时间）内容
	// public function get_finish($task_id)
	// {
	// 	$data = $this->field('finish')
	// 				 ->where('task_id = %d',$task_id)
	// 				 ->find();
	// 	if($data['finish'] == null)
	// 	{
	// 		return false;
	// 	}
	// 	else
	// 	{
	// 		return $data['finish'];
	// 	}
	// }
	//获取提交时间
	public function get_sub($task_id)
	{
		$data = $this->field('submit_time')
					->where('task_id = %d',$task_id)
					->find();
		return $data;
	}
	//获取完成任务ID和提交时间
	public function get_finish($userid)
	{
		$data = $this->field('task_id,submit_time')
					 ->where('gid = %d AND writed = 0 AND isupdata = 0 AND is_sp = 0',$userid)
					 ->select();
		return $data;
	}
	//获取待填写任务ID
	public function get_writed_id($userid)
	{
		$data = $this->field('task_id')
					 ->where('gid = %d AND writed = 1 AND isupdata = 0 AND is_sp = 0',$userid)
					 ->select();
		return $data['task_id'];
	}
	//获取待修改任务ID
	public function get_confirm_id($userid)
	{
		$data = $this->field('task_id')
					 ->where('gid = %d AND writed = 0 AND isupdata = 1 AND is_sp = 0',$userid)
					 ->select();
		return $data['task_id'];
	}
	//获取待审批任务ID
	public function get_sp_id($userid)
	{
		$data = $this->field('task_id')
					 ->where('gid = %d AND writed = 0 AND isupdata = 0 AND is_sp = 1',$userid)
					 ->select();
		return $data['task_id'];
	}


}




