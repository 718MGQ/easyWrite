<?php 
	namespace Home\Controller;
	use Think\Controller;
/**
 * 首页
 */
class IndexController extends Controller{
	
	public function index(){

		header("Content-Type:text/html;charset = utf-8");
		// session('gid', null);
  // 	   	session('pwd', null);
		$gid    = session('gid');
        $pwd    = session('pwd');
        
	    // var_dump(I("cookie."));die;
		$a = false;
	    $b = $_SERVER['HTTP_USER_AGENT'];
	    if(strpos($b, "MicroMessenger") === false)
	    {
	        if($gid != null && $pwd != null)
    			header("Location:/index.php/PC/User/dis#/home");
	    	else{
	    		setcookie("USERID", "", time() - 3600);
    			header("Location:/index.php/PC/User/dis");// 非微信浏览器
	    	}
	    }else
	    {
	    	//header("Location:/index.php/Home/User");// 微信浏览器，
	        //if($gid != null && $pwd != null)
	        	header("Location:/index.php/Home/User");
	    	// else
	    	// 	header("Location:/index.php/PC/User/dis");
	    }
    //$this->display();
    }
}

