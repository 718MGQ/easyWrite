<?php 
namespace Common\Model;
use Think\Model;

class TaskstatusModel extends Model
{
	protected $tableName = 'task_status';
	function get_data($gid)
	{
		$sql = '
			SELECT task.file_name, task.task_time, task.id, task_status.status, task_status.timestamp
			FROM task_status, task
			WHERE task_status.taskid = task.id AND task_status.gid = %d AND task_status.status <> 3
			ORDER BY task_status.taskid, task_status.timestamp
		';
		$datas = $this->query($sql, $gid);
		return $datas;
	}
}




 ?>