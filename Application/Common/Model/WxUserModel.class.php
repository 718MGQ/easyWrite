<?php 
namespace Common\Model;

use Think\Model;
const APPID = 'wx26d27c85a160dd5b';
const SECRET = '7bd31982da7b8e6294caeaa7aa4894fc';
const APPBASE = 'http://mgq.jblog.info/index.php/';
class WxUserModel extends Model
{
    protected $tableName = 'wx_user';


    function check($openid)
    {
        $sql = 'SELECT gid FROM wx_user WHERE openid = \'%s\'';
        $user = $this->query($sql, $openid);
        if(count($user) == 0)
        {
            return false;
        }else
        {
            return true;
        }
    }
    /*
        get user information
     */
    function get_info($openid)
    {
        $sql = 'SELECT teacher.gid, password, teacher.sex, headimgurl, name, nickname
                FROM wx_user,teacher
                WHERE wx_user.gid = teacher.gid
                AND openid = \'%s\'';
        $userinfo = $this->query($sql, $openid);
        if(count($userinfo) == 0)
        {
            return false;
        }else
        {
            $userinfo[0]['nickname'] = base64_decode($userinfo[0]['nickname']);
            return $userinfo;
        }
    }
    /*
    新增用户
     */
    function ins($openid, $sex, $headimgurl, $nickname, $create_time)
    {
        $nickname = base64_encode($nickname);
        $wx_user = array(
            'openid'      => $openid,
            'gid'         => -1,
            'sex'         => $sex,
            'headimgurl'  => $headimgurl,
            'subscribe'   => 1,
            'nickname'    => $nickname,
            'create_time' => $create_time
        );
        $this->add($wx_user);
    }
    //获取code
    //$redirect_uri   ->  模块/控制器/方法
    //$focused        ->  是否关注
    //$state          ->  重定向后会带上state参数
    function wx_code($redirect_uri, $state = "form")
    {
        $redirect_uri = urlencode(APPBASE.$redirect_uri);
        $scope = "snsapi_userinfo";
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . APPID . "&redirect_uri=" . $redirect_uri . "&response_type=code&scope=" . $scope . "&state=STATE#wechat_redirect";
        header('location:' . $url);
        exit();
    }
    

    //获取access_token、openID
    //返回值：
    //array(
    //      string   access_token
    //      string   openid
    //);
    //返回的正确的json数据包：
    //{ "access_token":"ACCESS_TOKEN",    
    // "expires_in":7200,    
    // "refresh_token":"REFRESH_TOKEN",    
    // "openid":"OPENID",    
    // "scope":"SCOPE" } 
    function wx_base($code)
    {  
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . APPID . '&secret=' . SECRET . '&code=' . $code . '&grant_type=authorization_code';
        $data = $this->curl($url);
        return json_decode($data, true);

    }
    /**
     * 获取用户信息
     * 参数列表
     *  string          $access_token   [微信登录第二步获取的token]
     *  string          $openid         [微信登录第二步获取的openid]
     * 返回结果
     *  array(
     *      string      nickname        [昵称]
     *      int         sex             [性别 0:未知,1:男,2:女]
     *      string      
     *  )
     *  array(
     *      int         errcode         [错误代码]
     *      string                      [错误信息]
     *  )
     */
    function wx_info($access_token, $openid)
    {
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
        $userinfo = $this->curl($url);
        return json_decode($userinfo, true);
    }
    /*通过access_token,openid获取用户信息
    返回结果
    {    "openid":" OPENID",  
    " nickname": NICKNAME,   
    "sex":"1",   
    "province":"PROVINCE"   
    "city":"CITY",   
    "country":"COUNTRY",    
    "headimgurl":    "http://wx.qlogo.cn/mmo//g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ
    4eMsv84eavHiaiceqxibJxCfHe/46",  
    "privilege":[ "PRIVILEGE1" "PRIVILEGE2"     ],    
    "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL" 
    } */
    function wx_user($access_token, $openid)
    {
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
        $user = $this->curl($url);
        return json_decode($user,true);
    }

    /**
     * 获取服务器access_token
     * 返回结果
     *  string          access_token    [access_token]
     *  array(
     *      int         errcode         [错误代码]
     *      string                      [错误信息]
     *  )
     */
    function wx_token()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . APPID . '&secret=' . SECRET;
        $token = $this->curl($url);
        $token = json_decode($token, true);
        if( ! isset($token['errcode']))
        {
            return $token['access_token'];
        }
        else
        {
            return $token;
        }
    }
    /**
     * 检查账号是否更改
     */
    function check_change($openid, $gid)
    {
        $sql = 'SELECT gid
                FROM wx_user
                WHERE openid = \'%s\'
        ';
        $gid_sql = $this->query($sql, $openid);
        if(count($gid_sql) !== 0 && $gid_sql[0]['gid'] !== $gid)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    /*
    微信openID与gid绑定
     */
    function upd($openid, $gid)
    {
        $sql = 'UPDATE wx_user SET gid = %d WHERE openid = \'%s\'';
        $this->execute($sql, $gid, $openid);
    }
    //爬取内容
    public function curl($url)
    {
        $curl = curl_init();

        // 设置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, $url);

        // 设置header
        curl_setopt($curl, CURLOPT_HEADER, 0);

        // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        // 运行cURL，请求网页
        $data = curl_exec($curl);

        // 关闭URL请求
        curl_close($curl);
        //返回获得的数据
        return $data;
    }
}

?>
