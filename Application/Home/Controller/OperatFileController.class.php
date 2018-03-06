<?php 
namespace Home\Controller;
use Think\Controller;

class OperatFileController extends 	Controller{

	public function operate(){
		$taskId = I('post.id');
		if($taskId == null){
			$this->ajaxReturn(0);
		}
		$
	}
}