<?php 
namespace PC\Controller;
use Think\Controller;

class IndexController extends Controller
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
		$this -> display();
		//   /index.php/pc/confirm
	}

 }


 



