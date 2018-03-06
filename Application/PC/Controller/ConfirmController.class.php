<?php
namespace PC\Controller;

use Think\Controller;
require_once 'ExcelFile.class.php';
class ConfirmController extends Controller
{
    /**
     * 首页
     * 文件自动加载，显示文件内容，
     * 点击下载即可下载，
     * 点击修改修改内容，点击提交保存数据
     * @return [type] [description]
     */
    public function fileLoad()
    {
        $string = json_decode(file_get_contents('php://input'), true);
        $filename = $string['filename'];
        $gid = session('gid');
        if (empty($gid)) {
            $res = array(
                'code'    => 2,
                'message' => '用户未登录',
                'data'    => array()
            );
            $this->ajaxReturn($res);
        }

        $filename = $gid.$filename;
        $readerController = A('ReaderFile');
        $data    =  $readerController->getFinalData($gid, $filename);
        if(isset($data['error'])){
            $this->ajaxReturn(
                array(
                    "code" => 1,
                    "message" => $data["message"],
                    "data" => array()
                )
            );
        }
        if(isset($data['Kind_errorCode'])){
            $this->ajaxReturn(array(
                "code" => 1,
                "message" => "此文件不支持本系统操作",
                "data" => []
            ));
        }
        $columns = $data[0];
        $result  = $data[1];
        //传到前端的$column([])
        $column  = array();
        for ($i = 0; $i < count($result[0]); $i++) {
            if($i === 0){
                $column[$i]['dataIndex'] = 'index';
            }
            // if(trim($result[0][$i]) == '序号' ){
            //     $column[$i]['dataIndex'] = 'index';
            // }
            $column[$i]['title'] = trim($result[0][$i]);
            for($j = 0;$j < count($columns);$j++){
                if($i === $columns[$j][1]){
                    $column[$i]['dataIndex'] = $columns[$j][0];
                }
            }
            if(!isset($column[$i]['dataIndex'])){
                $column[$i]['dataIndex'] = 'temp'.$i;
            }
        }
        $datasource = array();
        for ($i = 1; $i < count($result); $i++) {
            for ($j = 0; $j < count($result[$i]); $j++) {
                $t   = $column[$j]['dataIndex'];
                $datasource[$i - 1][$t] = $result[$i][$j];
            }
        }
        $form = array(
            'fileName'   => $filename,
            'columns'    => $column,
            'dataSource' => $datasource
        );
        $res = array(
            'code'    => 0,
            'message' => '读取成功',
            'data'    => $form
        );
        $this->ajaxReturn($res,'json');
    }
    /**
     * 做过修改以后选项 |0（保存数据库并写入Excel表）
     *                 |1（只写入Excel）
     * 
     * @return [type] [description]
     */
    public function contentCheck()
    {
        
        $string = json_decode(file_get_contents('php://input'), true);   //接受回传的数据
        // var_dump($string);die;
        $editCode       = (int)$string['editCode'];       //编辑码
        $filename       = $string['filename'];
        $dataSource     = $this -> restore_array($string['dataSource']);
        $columns        = $string['columns'];//没什么用处
        $gid = session('gid');
        if($editCode === 1){//更新到数据库并保存表

            $reader = A('ReaderFile');
            $data   = $reader->getFinalData($gid, $filename);
            $columnstandrad = $data['0'];
            $resultstandrad = $data['1']; //数据库中的原内容
            // print_r($resultstandrad);die;
            $tablename      = $data['2']; //对应数据表
            $count          = $data['count']; //数据库中的数据记录条数

            $update_sql = "UPDATE `".$tablename."` SET ";$where_and = " WHERE ";
        
            $dataSourceCount = count($dataSource);//前台做过更新的数据
            $sql = array(); $a  = 0;


            //逻辑应该是将每条数据都于数据库里查到的比较一下，如果没有就插入，如果有直接暴力更新
            //update_sql
            if($count > 0){
                for($i = 0;$i < $count;$i++)
                {
                    if($this->allLineIsNull($dataSource[$i]) == false){
                        for($j = 0,$columnstandrad_num = count($columnstandrad);$j < $columnstandrad_num;$j++){
                            if ($j < $columnstandrad_num - 1) {
                                $update_sql .= $columnstandrad[$j][0] . ' = ' . $this->getContentOrNull($dataSource[$i][$columnstandrad[$j][1]]) . ' , ';
                                $where_and .= $columnstandrad[$j][0].'='.$this ->getContentOrNull($resultstandrad[$i+1][$columnstandrad[$j][1]]).' and ';
                            } else {
                                $update_sql .= $columnstandrad[$j][0] . ' = ' . $this->getContentOrNull($dataSource[$i][$columnstandrad[$j][1]]);
                                $where_and .= $columnstandrad[$j][0].'='.$this ->getContentOrNull($resultstandrad[$i+1][$columnstandrad[$j][1]]); 
                            }
                        }
                        $sql[$a++] = $update_sql.$where_and;
                        $update_sql = "UPDATE `".$tablename."` SET ";
                        $where_and = " WHERE ";
                    }
                }

                $update_sqlAll = '';
                for ($updateI = 0; $updateI < count($sql); $updateI++) {
                    $update_sqlAll = $update_sqlAll . $sql[$updateI] . ';';
                }
                try{
                    $updateExecuteRows = M($tablename)->execute($update_sqlAll); //返回更新的行数；没有更新就返回零
                }catch(Exception $e){
                    $updateExecuteRows = -1;//如果更新异常就将更新的行数默认-1
                }
            }
            $sql = array();$a  = 0;
            //$insertssql;
            $sqlInsert  = 'INSERT INTO ' . $tablename . ' (gid,';$va  = $gid . ',';
            for ($i = $count,$dataSourceCount = count($dataSource); $i < $dataSourceCount; $i++) {//剩余行数未填写或全部填写（INSERT）;
              if ($this->allLineIsNull($dataSource[$i]) == false) {
                for($j = 0,$columnstandrad_num = count($columnstandrad);$j < $columnstandrad_num;$j++){
                    if ($j < $columnstandrad_num - 1) {
                        if ($this->checkStr($columnstandrad[$j][0], 'category') && $dataSource[$i][$columnstandrad[$j][1]] != null) {
                            $sqlInsert .= $columnstandrad[$j][0] . ',';
                            $va .= $this->getIdByCategory($dataSource[$i][$columnstandrad[$j][1]]) . ',';
                        } else {
                            $sqlInsert .= $columnstandrad[$j][0] . ',';
                            $va .= $this->getContentOrNull($dataSource[$i][$columnstandrad[$j][1]]) . ',';
                        }
                    } else {
                        if ($this->checkStr($columnstandrad[$j][0], 'category') && $dataSource[$i][$columnstandrad[$j][1]] != null) {
                            $sqlInsert .= $columnstandrad[$j][0] . ')';
                            $va .= $this->getIdByCategory($dataSource[$i][$columnstandrad[$j][1]]);
                        } else {
                            $sqlInsert .= $columnstandrad[$j][0] . ')';
                            $va .= $this->getContentOrNull($dataSource[$i][$columnstandrad[$j][1]]);
                        }
                    }
                  }
                  $sql[$a++] = $sqlInsert . ' VALUES ' . '(' . $va . ')';
                  $sqlInsert = 'INSERT INTO ' . $tablename . ' (gid,';
                  $va        = $gid . ',';
              }
            }
            $insert_sqlAll = '';
            for ($insertI = 0; $insertI < count($sql); $insertI++) {
                $insert_sqlAll = $insert_sqlAll . $sql[$insertI] . ';';
            }
            if($insert_sqlAll == '')
            {
                $insertExecuteRows = 0;
            }else{
                try{
                    $insertExecuteRows = M($tablename)->execute($insert_sqlAll); //返回插入语句影响的行数
                }catch(Exception $e){
                    $insertExecuteRows = -1;
                }
            }
            //重组data,便于写入表格
            $write_data = array();
            $i = 0;$j = 0;
            foreach ($columns as $key => $value) {
                $write_data[$i][$j] = $value['title'];
                $j++;
            }
            $i++;
            foreach($dataSource as $key => $value)
            {
                $j = 0;
                foreach($dataSource[$key] as $k => $value){
                    $write_data[$i][$j] = $dataSource[$key][$k];  
                    $j++;  
                }
                $j = 0;$i++;
            }
            // echo "<pre>";
            // print_r($write_data);die;
            $Excel = new ExcelFile();
            $bool = $Excel -> ExcelWriter($write_data,$filename);
            if ($updateExecuteRows !== -1 && $insertExecuteRows !== -1 && $bool) {
                $this->ajaxReturn(
                    array(
                    'code'    => 0,
                    'message' => '提交成功',
                    'data'    => array()
                ));
            }else
            {
                $this->ajaxReturn(
                    array(
                        'code' => 1,
                        'message' => "修改出错",
                        'data' => array()
                    )
                );
            }
        }else{
            $write_data = array();
            $i = 0;$j = 0;
            foreach ($columns as $key => $value) {
                $write_data[$i][$j] = $value['title'];
                $j++;
            }
            $i++;
            foreach($dataSource as $key => $value)
            {
                $j = 0;
                foreach($dataSource[$key] as $k => $value){
                    $write_data[$i][$j] = $dataSource[$key][$k];  
                    $j++;  
                }
                $j = 0;$i++;
            }
            // print_r($write_data);die;
            $Excel = new ExcelFile();
            $bool = $Excel -> ExcelWriter($write_data,$filename);


            if($bool)
            {
                $this->ajaxReturn(array(
                    "code" => 0,
                    "message" => "保存成功",
                    "data" => array()
                ));
            }else{
                $this->ajaxReturn(array(
                    "code" => 1,
                    "message" => "保存失败",
                    "data" => array()
                ));
            }
        }

    }

	/**
	 * 文件分类显示
	 * @return [type] [description]
	 */
    public function index()
    {
        /*
        code :
        message :
        data : array();

        {
        "code":0,
        "message":"",
        "data":
        {"unwrite": {
        "num": "3",
        "data": [
        {
        "file_name": "专利信息调查表",
        "id": "3",
        "submit_time": "待填写",
        "task_time": "2017-08-01 18:00:00",
        "remark": "手写版+电子版",
        "status": "待填写",
        "operation": "去填写"
        },
        {
        "file_name": "专利信息调查表",
        "id": "2",
        "submit_time": "待填写",
        "task_time": "2017-08-01 12:00:00",
        "remark": "手写版+电子版",
        "status": "待填写",
        "operation": "去填写"
        },
        {
        "file_name": "教师专利信息统计表",
        "id": "3",
        "submit_time": "待填写",
        "task_time": "2017-07-31 18:00:00",
        "remark": "手写版+电子版",
        "status": "待填写",
        "operation": "去填写"
        }
        ]
        },
        "unconfirm": {
        "num": "1",
        "data": [
        {
        "file_name": "教师专利信息统计表",
        "id": "4",
        "submit_time": "待修改",
        "task_time": "2017-08-01 18:00:00",
        "remark": "学校要用全称",
        "status": "审核不通过",
        "operation": "去修改"
        }
        ]
        },
        "unchecked": {
        "num": "1",
        "data": [
        {
        "file_name": "教师专利信息统计表",
        "id": "5",
        "submit_time": "2017-08-01 13:00:00",
        "task_time": "2017-08-01 18:00:00",
        "remark": "学校要用全称",
        "status": "待审批",
        "operation": "去修改"
        }
        ]
        },
        "finished": {
        "num": "4",
        "data": [
        {
        "file_name": "教师专利信息统计表",
        "id": "6",
        "submit_time": "2017-07-20 18:00:00",
        "task_time": "已截止",
        "remark": "学校要用全称",
        "status": "已完成",
        "operation": "去查看"
        },
        {
        "file_name": "教师专利信息统计表",
        "id": "7",
        "submit_time": "2017-06-29 18:00:00",
        "task_time": "已截止",
        "remark": "学校要用全称",
        "status": "已完成",
        "operation": "去查看"
        },
        {
        "file_name": "教师专利信息统计表",
        "id": "8",
        "submit_time": "2017-05-30 18:00:00",
        "task_time": "已截止",
        "remark": "学校要用全称",
        "status": "已完成",
        "operation": "去查看"
        },
        {
        "file_name": "教师专利信息统计表",
        "id": "9",
        "submit_time": "2017-04-30 18:00:00",
        "task_time": "已截止",
        "remark": "学校要用全称",
        "status": "已完成",
        "operation": "去查看"
        }
        ]
        }
        }
        }
         */
        $step = array(
            0 => 'unwrited',
            1 => 'unconfirm',
            2 => 'unchecked',
            3 => 'finished',
        );
        // session值为空问题，待解决
        $gid = session('gid');
        // die($gid);
        //var_dump($gid);
        //$gid = 130001;
        //       var_dump($_COOKIE);die;
        //      echo "<pre>";
        // print_r($_SESSION);
        //     echo "<br>";
        // var_dump($gid);die;
        if($gid == null)
        {
            $task_arr = array(
                    'code'    => 2,
                    'message' => "登录超时",
                    'data'    => null,
            );
            $this->ajaxReturn($task_arr);
        }
        
        $data = D('Taskinfo')->get_pc($gid); 
        //filename,task_id,submit_time,task_time,writed,isupdata,is_sp
        $task_arr = array();

        $code    = 0;
        $message = "";
        $unwrite = array(
            'num'  => 0,
            'data' => array(),
        );
        $unconfirm = array(
            'num'  => 0,
            'data' => array(),
        );
        $unchecked = array(
            'num'  => 0,
            'data' => array(),
        );
        $finished = array(
            'num'  => 0,
            'data' => array(),
        );

        if (count($data != 0)) {
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['writed'] == 1) {
                    $data[$i]['status']    = '待填写';
                    $data[$i]['operation'] = '去填写';
                    $data[$i]['remark']    = '名称写全称';
                    unset($data[$i]['writed']);
                    unset($data[$i]['is_sp']);
                    unset($data[$i]['isupdata']);
                    $data[$i]['id'] = $data[$i]['task_id'];
                    unset($data[$i]['task_id']);
                    $data[$i]['submit_time'] = date("Y-m-d H:i:s", $data[$i]['submit_time']);
                    $data[$i]['task_time']   = date("Y-m-d H:i:s", $data[$i]['task_time']);
                    $unwrite['num']++;
                    $unwrite['data'][] = $data[$i];

                } else if ($data[$i]['isupdata'] == 1) {
                    $data[$i]['status']    = '待修改';
                    $data[$i]['operation'] = '去修改';
                    $data[$i]['remark']    = '名称写全称';
                    unset($data[$i]['writed']);
                    unset($data[$i]['is_sp']);
                    unset($data[$i]['isupdata']);
                    $data[$i]['id'] = $data[$i]['task_id'];
                    unset($data[$i]['task_id']);
                    $data[$i]['submit_time'] = date("Y-m-d H:i:s", $data[$i]['submit_time']);
                    $data[$i]['task_time']   = date("Y-m-d H:i:s", $data[$i]['task_time']);
                    $unconfirm['num']++;
                    $unconfirm['data'][] = $data[$i];
                } else if ($data[$i]['is_sp'] == 1) {
                    $data[$i]['status']    = '待审批';
                    $data[$i]['operation'] = '等待审批';
                    $data[$i]['remark']    = '名称写全称';
                    unset($data[$i]['writed']);
                    unset($data[$i]['is_sp']);
                    unset($data[$i]['isupdata']);
                    $data[$i]['id'] = $data[$i]['task_id'];
                    unset($data[$i]['task_id']);
                    $data[$i]['submit_time'] = date("Y-m-d H:i:s", $data[$i]['submit_time']);
                    $data[$i]['task_time']   = date("Y-m-d H:i:s", $data[$i]['task_time']);
                    $unchecked['num']++;
                    $unchecked['data'][] = $data[$i];
                } else if ($data[$i]['writed'] == 0 && $data[$i]['isupdata'] == 0 && $data[$i]['is_sp'] == 0) {
                    $data[$i]['status']    = '已完成';
                    $data[$i]['operation'] = '去完成';
                    $data[$i]['remark']    = '名称写全称';
                    unset($data[$i]['writed']);
                    unset($data[$i]['is_sp']);
                    unset($data[$i]['isupdata']);
                    $data[$i]['id'] = $data[$i]['task_id'];
                    unset($data[$i]['task_id']);
                    $data[$i]['submit_time'] = date("Y-m-d H:i:s", $data[$i]['submit_time']);
                    $data[$i]['task_time']   = date("Y-m-d H:i:s", $data[$i]['task_time']);
                    $finished['num']++;
                    $finished['data'][] = $data[$i];
                }

            }
        }

        $datas = array(
            'unwrite'   => $unwrite,
            'unconfirm' => $unconfirm,
            'unchecked' => $unchecked,
            'finished'  => $finished
        );
        $task_arr = array(
            'code'    => $code,
            'message' => $message,
            'data'    => $datas
        );
        // print_r($task_arr);die;
        $this->ajaxReturn($task_arr);
    }

    //文件上传
    /*
    文件错误编码
    /其值为 0，没有错误发生，文件上传成功。 

    其值为 1，上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。 

    其值为 2，上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。 

    其值为 3，文件只有部分被上传。 

    其值为 4，没有文件被上传。 

    其值为 6，找不到临时文件夹。PHP 4.3.10 和 PHP 5.0.3 引进。 

    其值为 7，文件写入失败。PHP 5.1.0 引进。
    */
    public function upload()
    {
        $filename = iconv("utf-8","gb2312",$_FILES["file"]["name"]);
        $uptype = array("xlsx","xls","docx","doc");
        header('Content-type: text/html; charset=utf-8');
        $gid = session('gid');
        //print_r($_FILES);
        // $filename=$_FILES['file']['name'];
        // 文件error码
        $sp = array(
                0 => "上传成功",
                1 => "上传文件过大",
                2 => "上传文件过大",
                3 => "文件只有部分上传",
                4 => "没有文件上传",
                6 => "找不到文件",
                7 => "文件读取失败"
            );
        $code = $_FILES["file"]["error"];
        if ($code != 0)
        {

            // header('HTTP/1.1 500 Internal Server Error');
            $this->ajaxReturn(
                    array(
                        "code"    => 1,
                        "message" => $sp[$code],
                        "filename"=>""
                    )
                );
            // echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
        }
        else
        {

            // header('HTTP/1.1 500 Internal Server Error');
            $torrent = explode(".", $filename);
            $fileend = end($torrent);
            $fileend = strtolower($fileend);
            if(!in_array($fileend, $uptype))
            //检查上传文件类型
            {
                header('HTTP/1.1 500 Internal Server Error');
                $this->ajaxReturn(
                        array(
                            "code"    => 1,
                            "message" => "文件类型错误",
                            "filename"=> ""
                        )
                    );
                // exit;
            }
            if (file_exists(realpath('./') . '\\'.'tasktable\\' . $gid.$filename))
            {
                // echo $filename . " 已存在 ";
                header('HTTP/1.1 500 Internal Server Error');
                $this->ajaxReturn(
                        array(
                            "code"    => 1,
                            "message" => "文件已存在",
                            "filename"=> $_FILES["file"]["name"]
                        )
                    );
            }
            else
            {

                // header('HTTP/1.1 200 OK');
                move_uploaded_file($_FILES["file"]["tmp_name"],
                                    realpath('./') .'\\'. 'tasktable\\' . $gid.$filename);
                // echo realpath('./') . "tasktable/" . $filename."上传成功";
                $task_im = array(
                                'file_name' => $_FILES["file"]["name"],
                                'task_time' => time(),
                                'upfile_gid' => $gid,
                                'file_url' => realpath('./') .'\\'. 'tasktable\\' .  $gid.$_FILES["file"]["name"],
                                'settasktime' => time()

                            );
                M("Task")->data($task_im)->add();
                $this->ajaxReturn(
                        array(
                            "code"    => 0,
                            "message" => "上传成功",
                            "filename"=> $_FILES["file"]["name"]
                        )
                    );
            }
        }
        
    }
    //刷新
    public function refresh()
    {
        $gid = session("gid");
        // $gid = 130000;
        // var_dump($gid);die;
        $list = D("Task")->get_file_name($gid);
        $data = array(
                "file_exists_name" => $list
            );
        $this->ajaxReturn(
            array(
                "code" => 0,
                "message" => "",
                "data" => $data
            )
        );
    }
    //修改密码
    public function setPasswd()
    {
        $gid = session("gid");
        // $gid = "130001";
        $data = json_decode(file_get_contents('php://input'), true);
        $old_passwd = $data["oldPassword"];
        $new_passwd = $data["newPassword"];
        if(D("Teacher")->check($gid, $old_passwd))
        {
            if($old_passwd !== $new_passwd)
            {
                D("Teacher")->setPassWd($gid, $new_passwd);
                session('pwd',$new_passwd);
            }
            $this->ajaxReturn(
                    array(
                        'code' => 0,
                        'message' => "密码修改成功！"
                    )
            );
        }else
        {
            $this->ajaxReturn(
                    array(
                        'code' => 1,
                        'message' => "密码错误!"
                    )
            );
        }
    }

    /**
     * 索引数字化
     * @param  [type] $array [description]
     * @return [type] $array [description]
     */
    public function restore_array($array)
    {
        $arr = array();
        $i   = 0;
        $j   = 0;
        foreach ($array as $key => $value) {
            foreach ($array[$key] as $k => $va) {
                $arr[$i][$j] = $va;
                $j++;
            }
            $i++;
            $j = 0;
        }
        return $arr;
    }
    /**
     * 若全为空，返回true.否则返回false;
     * @param  $array $dataSource
     * @return bool
     */
    public function allLineIsNull($dataSource)
    {
        $count = count($dataSource);
        for ($i = 1; $i < $count; $i++) {
            if (!empty($dataSource[$i])) {
                return false;
            }
        }
        return true;
    }
    /**
     * 判断是否为字符串且返回为带单引号的字符串
     * @param  [type] $a [description]
     * @return [type]    [description]
     */
    public function getContentOrNull($a)
    {
        if (is_string($a)) {
            $a = '\'' . $a . '\'';
            return $a != null ? $a : 'null';
        } elseif (is_numeric($a)) {
            return $a != null ? $a : 'null';
        } elseif ($a == null) {
            return 'null';
        }
    }
    /**
     * 针对类别项，找到id
     * @param  [type] $category [description]
     * @return [type]           [description]
     */
    public function getIdByCategory($category)
    {
        $id = D('Allcolumns')->getId($category);
        return $id;
    }
    /**
     * 检查一个字符串中是否包含另一个字符串
     * @param  [type] $string [description]
     * @param  [type] $target [description]
     * @return [type]         [description]
     */
    public function checkStr($string, $target)
    {
        $tmp = strpos($string, $target);
        //print_r($tmpArr);
        if ($tmp !== false) {
            return true;
        } else {
            return false;
        }

    }
}
