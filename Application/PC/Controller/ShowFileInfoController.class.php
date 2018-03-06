<?php
namespace PC\Controller;

use Think\Controller;

class ShowFileInfoController extends Controller
{
    /**
     * 点击表名显示的表的数据内容
     * @return [type] [description]
     */
    public function tableData()
    {
        // die($_SERVER['HTTP_REFERER']);
        $data   = json_decode(key($_REQUEST), true);
        $taskId = $data['id'];
        $gid   = session('gid');
        if (empty($gid)) {
            $res = array(
                'code'    => 2,
                'message' => '用户未登录',
                'data'    => array()
            );
            $this->ajaxReturn($res);
        }
        $task     = D('Task');
        $status   = $task->getStatus($gid, $taskId);
        $filename = $task->get_name($taskId);
        $filename .= '.xlsx';
        if (empty($filename)) {
            $res = array(
                'code'    => 1,
                'message' => '文件未找到',
                'data'    => array()
            );
            $this->ajaxReturn($res);
        }
        // echo $filename;die;
        $reader = A('ReaderFile');
        $data    = $reader->getFinalData($gid, $filename);
        $columns = $data[0];
        $result  = $data[1];
        $column  = array();
        for ($i = 0; $i < count($result[0]); $i++) {
            if ($result[0][$i] == '序号') {
                $column[$i]['title']     = $result[0][$i];
                $column[$i]['dataIndex'] = 'index';
            } else {
                $column[$i]['title']     = $result[0][$i];
                $column[$i]['dataIndex'] = $columns[$i - 1][0];
            }
        }
        $datasource = array();
        for ($i = 1; $i < count($result); $i++) {
            for ($j = 0; $j < count($result[$i]); $j++) {
                $t                      = $column[$j]['dataIndex'];
                $datasource[$i - 1][$t] = $result[$i][$j];
            }
        }
        $form = array(
            'fileName'   => $filename,
            'version'    => time(),
            'status'     => $status,//0表示要修改
            'columns'    => $column,
            'dataSource' => $datasource
        );
        $res = array(
            'code'    => 0,
            'message' => '',
            'data'    => $form
        );
        // echo '<pre>';
        // print_r($res);die;
        $this->ajaxReturn($res, 'json');
    }
    /**
     * 文档处理页面，收前端回传的taskid及文档array，对比数据库中的信息
     * 提取有效信息，写入数据库。
     * 删除原有数据，把接收的array写入
     * @return [type] [description]
     */
    public function checkFileContent()
    {
        $string = json_decode(file_get_contents('php://input'), true);
        $gid = session('gid');
        if (empty($gid)) {
            $this->ajaxReturn(
                array(
                    'code'    => 2,
                    'message' => 'The userSession has out of date',
                    'data'    => array()
                ));
        }
        $username = D('Teacher')->getNameBygid($gid);
        //获取到前台传的更新过的表content.
        $columns    = $string['columns'];
        $result     = $string['dataSource'];
        $dataSource = $this->restore_array($result);

        $taskId   = (int) $string['id']; //任务ID
        $task     = D('Task');
        $filename = $task->get_name($taskId);
        $filename .= '.xlsx';
        die($filename);
        //返回数据库中的原数据
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
    public function test()
    {
        $str = '国家自然科学技术发明二等奖';

        if ($this->checkStr('categoryid', 'category')) {
            $id = $this->getIdByCategory($str);
        }
        echo $id;
    }
}
