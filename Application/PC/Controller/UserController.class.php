<?php
namespace PC\Controller;

use Think\Controller;

class UserController extends Controller
{
    public function index()
    {
        $a = false;
        $b = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($b, "MicroMessenger") === false) {
            $a = false; // 非微信浏览器
        } else {
            $a = true; // 微信浏览器，
        }
        if ($a) {
            $url = "http://mgq.jblog.info/index.php/Home/User";
        } else {
            $url = "http://mgq.jblog.info/index.php/PC/User/dis#/home";
        }
        $data = json_decode(key($_REQUEST), true);
        $gid = $data['userName'];
        $pwd = $data['password'];
        if (D("Teacher")->check($gid, $pwd)) {
            session('gid', $gid);
            session('pwd', $pwd);
            $data = array(
                'code'    => 0,
                'message' => '',
                'data'    => array('url' => $url,
                					'id' => $gid
            					)
            );
            if ($a) {
                $data['code'] = 3;
            }
            $this->ajaxReturn($data, 'JSON');
        } else {
            $data = array(
                'code'    => 1,
                'message' => '账号或密码错误',
                'data'    => ''
            );
            $this->ajaxReturn($data, 'JSON');
        }
    }

    public function dis()
    {
        $this->display('index');
    }
    
}
