<?php
namespace Common\Model;

use Think\Model;

class TeacherModel extends Model
{
    protected $tableName = 'teacher';

    public function getInfo($gid)
    {
        $sql = "SELECT gid,name,password,accountNum,title,dept,sex,birthday,idCard,email,mobile,telephone,country,origin,education,isWork
                from teacher
                where gid = " . $gid;
        //echo $sql;exit;
        $data = $this->query($sql);
        if ($data[0]['sex'] == 0) {
            $data[0]['sex'] = '男';
        } else {
            $data[0]['sex'] = '女';
        }
        return $data;
    }
    public function check($gid, $pwd)
    {
        $sql  = 'SELECT gid,password FROM teacher WHERE gid= %s AND password= %s';
        $user = $this->query($sql, $gid, $pwd);
        if (count($user) == 0) {
            return false;
        } else {
            return true;
        }
    }
    public function getNameBygid($gid)
    {
        $sql = 'SELECT * from teacher where gid = ' . $gid;
        //die($sql);
        $username = $this->query($sql);
        return $username[0]['name'];
    }
    //修改密码
    public function setPassWd($gid, $password)
    {
        $sql = 'UPDATE teacher set password = %s where gid = %s';
        $this->execute($sql, $password, $gid);
    }
}
