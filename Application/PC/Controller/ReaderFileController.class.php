<?php
namespace PC\Controller;

use Think\Controller;
require_once 'ExcelFile.class.php';
class ReaderFileController extends Controller
{

    // public function readFile()
    // {

    //     header("Content-Type:text/html;charset = utf-8");
    //     $filename1 = "专利信息调查表.xlsx";
    //     $filename  = iconv("utf-8", "gb2312", 'D:\XAMPPIN\htdocs\GRepository\html\tasktable\\'.$filename1);
    //     // exit($filename);
    //     import("Vendor.PHPExcel.PHPExcel");
    //     import("Vendor.PHPExcel.Writer.Excel2007");
    //     import("Vendor.PHPExcel.PHPExcel.IOFactory");
    //     $inputFileType = \PHPExcel_IOFactory::identify($filename);
    //     $objReader     = \PHPExcel_IOFactory::createReader($inputFileType);

    //     $obj = $objReader->load($filename); //全部加载文件

    //     $data = $obj->getActiveSheet()->toArray();
    //     $res = array_values($this->readFileFilter($data));
    //     $kind = $this->getFileKinds($filename1); //表的关键字

    //     $tableinfo = $this->getTableInfoByKinds($kind); //数据库的表
    //     $columns = $this->getColumn($tableinfo, $res);
    //     $dataInfo = $this->getDataInfo($tableinfo, $columns, 130001);
    //     $result = $this->WriteInto($dataInfo, $res, $columns);

    //     $objPHPExcel = new \PHPExcel();
    //     $objSheet    = $objPHPExcel->getActiveSheet(); //选取当前的sheet对象
    //     $objSheet->setTitle('one');
    //     $objSheet->fromArray($result); //echo "success";
    //     $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); //设定写入excel的类型
    //     $saveurl   = realpath('./').'\tasktableDone\\' . '专利信息调查表.xlsx';
    //     $objWriter->save($saveurl); //保存文件
    //     echo '111';
    //     die;
    // }
    // public function readPath(){
    //     $path = realpath('./').'\tasktable\\';
    //     echo $path;
    // }
    public function getFinalData($gid, $filename1)
    {        
        $kind  = $this->getFileKinds($filename1); //表的关键字
        if($kind === null){
            return array('Kind_errorCode' => 0);
        }
        $tableinfo = $this->getTableInfoByKinds($kind); //数据库的表
        // $encode = mb_detect_encoding($filename1, array("ASCII","UTF-8","GB2312","GBK",'BIG5')); 
        // echo $encode;die;

        $file = new ExcelFile();
        $data = $file->FileReader($filename1);
        // echo "<pre>";print_r($data);die;
        if(isset($data['error'])){
            return $data;
        }
        $res       = array_values($this->readFileFilter($data));
        $columns  = $this->getColumn($tableinfo, $res);
        if($columns === false)
        {
            return ['error' => 1,"message" => '没有有效信息'];
        }
        $dataInfo = $this->getDataInfo($tableinfo, $columns, $gid);
        $num      = $dataInfo['count'];
        $result   = $this->WriteInto($dataInfo, $res, $columns);
        $data     = array(
            '0'     => $columns,
            '1'     => $result,
            '2'     => $tableinfo['table_name'],
            'count' => $num
        );
        return $data;
    }
    //填充数据
    public function WriteInto($content, $res, $columns)
    {
        for ($i = 0; $i < count($content); $i++) {
            $a = 0;
            for ($j = 0; $j < count($columns); $j++) {
                $res[$i + 1][$columns[$j][1]] = $content[$i][$a];
                $a++;
            }
        }
        return $res;
    }
    //过滤空单元
    public function readFileFilter($data)
    {
        $res = array();
        for ($i = 0, $k = count($data); $i < $k; $i++) {
            for ($j = 0, $s = count($data[$i]); $j < $s; $j++) {
                if ($data[$i][$j] !== null) {
                    $res[$i][$j] = $data[$i][$j];
                } else {
                    $res[$i][$j] = null;
                }
            }
        }
        return $res;
    }

    public function getUser()
    {
        return session['user'];
    }
    // 获得表名关键字
    public function getFileKinds($filename)
    {
        $kind = null;
        $kindarr = array("学术竞赛", "课程信息", "创新团队", "专利", "学科竞赛", "优秀人才", "专著", "教材", "基本信息", "教改", "科研", "教研成果", "教学团队", "论文");
        for ($i = 0, $k = count($kindarr); $i < $k; $i++) {
            if (strstr($filename, $kindarr[$i]) == null) {
                continue;
            } else {
                $kind = $kindarr[$i];
            }
        }
        return $kind;
    }
    //找到对应表
    public function getTableInfoByKinds($filekinds)
    {

        $alltable = D('Alltable');
        $data     = $alltable->getTableInfo($filekinds);
        return $data;
    }
    //通过解析文件后的"结果集"和"文件对应的表",找到对应表关键字和字段名,返回所填表所填项对应的字段
    //返回查询得到的数据
    public function getDataInfo($tableInfo, $columns, $gid)
    {
        $allcolumns = D('Allcolumns');
        $data       = $allcolumns->getColumnsContents($gid, $columns, $tableInfo['table_name']);
        return $data;
    }
    //返回需要的字段
    public function getColumn($tableInfo, $res)
    {
        $allcolumns = D('Allcolumns');
        $columns    = $allcolumns->getColumns($tableInfo, $res);
        return $columns;
    }

}
