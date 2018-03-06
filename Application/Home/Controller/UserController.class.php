<?php
namespace Home\Controller;

use Think\Controller;

const APPBASE = 'http://mgq.jblog.info';

/**
 * 微信浏览器用户登录
 */
class UserController extends Controller
{

    protected $wx_user;

    public function __construct()
    {
        parent::__construct();
        $this->wx_user = D('WxUser');
    }#finished

    /**
     * 根据登陆状态执行方法
     */
    public function index()
    {
        $gid    = session('gid');
        $pwd    = session('pwd');
        $openid = session('openid');
        if($gid !== null && $pwd !== null)
        {
            $this->in($gid, $pwd);
        }
        else if($openid !== null) 
        {
            $this->in_by_openid($openid);
        }
        else
        {
            $this->wx_in();
        }
    }#finished

    /**
     * 注销
     */
    public function sign_out()
    {
        session('gid', null);
        session('pwd', null);
    }#finished

    /**
     * 微信登陆
     */
    public function wx_in()
    {
        $code  = I('get.code', null);
        $state = I('get.state', null);
        if($code === null)
        {
            $this->wx_user->wx_code('Home/User/wx_in');
            return;
        }
        $base = $this->wx_user->wx_base($code);
        if( ! isset($base['openid']))
        {
            $this->sign_in();
            return;
        }
        $openid = $base['openid'];
        session('openid', $openid);
        if($this->wx_user->check($openid))
        {
            $this->in_by_openid($openid);
            return;
        }
        $info = $this->wx_user->wx_info($base['access_token'], $openid);
        if(isset($info['errcode']))
        {
            if($info['errcode'] === 48001)
            {
                $this->wx_in(false);
                return;
            }
            $this->sign_in();
            return;
        }
        date_default_timezone_set('PRC');//设置中国时区
        session('sex', $info['sex']);
        session('headimgurl', $info['headimgurl']);
        session('nickname', $info['nickname']);
        $this->wx_user->ins($info['openid'], $info['sex'], $info['headimgurl'], $info['nickname'], time());
        $this->sign_in();
    }#finished

    /**
     * 通过openid登陆
     */
    public function in_by_openid($openid)
    {
        $info = $this->wx_user->get_info($openid);
        if($info === false)
        {
            $this->sign_in();
            return;
        }
        $this->in($info['gid'], $info['pwd']);
    }#finished


    /**
     * 登陆
     */
    public function in($gid = null, $pwd = null)
    {
        if($gid === null || $pwd === null)
        {
            header("Location:/index.php/PC/User/dis");
        }
        // if($gid === null || $pwd === null)
        // {
        //     $this->index();
        //     return;
        // }
        // $pwd = md5($pwd);
        // session('gid', $gid);
        // session('pwd', $pwd);
        $openid = session('openid');
        if($openid === null)
        {
            $this->wx_in();
        }
        if($openid !== null && $this->wx_user->check_change($openid, $gid))
        {
            $this->wx_user->upd($openid, $gid);
            //$this->send_band($openid, $gid);
        }
        $this->dis();
    }#finished
    /**
     * 加载登陆页
     */
    public function sign_in()
    {
        header("Location:/index.php/PC/User/dis");

    }#finished

    public function dis()
    {
        $this->display('index');
    }
    //发送模板消息
    /*function send_band($openid, $acc)
    {
        $access_token = $this->get_token();
        if($access_token === false)
        {
            return false;
        }
        $template_id = 'Opw5l9pNNPJKu_Pm7tJVvdFcT02AKE1jKRqOyf5BQnk';
        $url = 'jblog.info/nefuer';
        $data = array(
            "first"=> array(
                "value"=>"您已为该微信绑定学号",
                "color"=>"#173177"
            ),
            "keyword1"=>array(
                "value"=>$acc,
                "color"=>"#173177"
            ),
            "keyword2"=> array(
                "value"=>date('Y-m-d H:i:s'),
                "color"=>"#173177"
            ),
            "remark"=>array(
                "value"=>"此微信账号查成绩无需手动填写密码（密码已修改除外）",
                "color"=>"#173177"
            )
        );
        $result = $this->wx_user->wx_teminfo($openid, $access_token, $template_id, $url, $data);
        return();
    }*/

} 