<?php

命名

	PHP相关

		类名采用 大驼峰 法命名
		class MyClassName{}

		类成员变量采用 小驼峰 法命名
		protected $myClassVar;

		方法、变量采用 全小写+下划线 法命名
		$my_var;
		function this_is_a_fun(){}

		常量采用 全大写+下划线 法命名
		MAX_NUM MIN_SIZE

	数据库相关

		数据库名、表名、字段名全部采用 全小写+下划线 法命名
		my_database wx_table create_time

SQL语句

	除字段、值等为小写，其他全部大写
	每段FROM、WHERE、AND等均换行书写
	AND、OR、子查询需缩进书写
	除特殊情况，禁止使用 * 作为查询字段
	SELECT mid
	FROM wx_table
	WHERE id < 12
		AND name = 'ace'
		OR (
			mid < 12 AND u_name <> 'df'
		)
	ORDER BY id DESC
	LIMIT 2

	禁止在插入语句省略字段列表
	//INSERT INTO v_table VALUES(d, s, e) ×
	INSERT INTO v_table(a, b, c)
	VALUES('3', 3)


THINKPHP数据库方法

	每个方法回车、对齐书写	
	$this->field()
		 ->where('d < %d', 2)
		 ->find()

*禁止在循环内执行sql语句、执行TP的数据库方法
	可在一个语句中用JOIN或其他方法获取所需内容，再在php内处理

注释

	每个方法前写明该方法名称
	/**
	 * 加载登录页面
	 */
	function login(){}

	涉及ajax返回结果码，写出码列表及对应意义
	/**
	 * 登录
	 * 结果码|码意义
	 * 0 | 账号不存在
	 * 1 | 密码错误
	 * 2 | 验证码错误
	 * 3 | 登录成功
	 */
	function do_login(){}

	应在程序段内适当添加注释
	function login()
	{
		//接收信息
		$acc = I('post.acc', null);
		$pwd = I('post.pwd', null);
		//判断信息正确性
		if($acc === null || $pwd === null)
		{
			$this->ajaxReturn(0);
		}
		//验证密码正确性
		$pwd_sql = D('user')->get_pwd($acc);
		if(password_verify($pwd, $pwd_sql))
		{
			$this->ajaxReturn(1);
		}
		else
		{
			$this->ajaxReturn(0);
		}
	}

其他
	
	判断时尽量使用 === !== 来判断是否相等，否则可能会出现 0 == null 结果为真的情况

	= + - * / . 等符号两边留空
	$a = $b + $c;

	值列表 , ; 后留空
	if($i = 0, $len = count($l); $i < $len; $i++){}

	! 符号两边留空
	if(isset($_GET['b'])){}
	if( ! isset($_GET['a'])){}  

	大括号位置不做要求
