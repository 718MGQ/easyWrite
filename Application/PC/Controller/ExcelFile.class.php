<?php

namespace PC\Controller;

use Think\Controller;
include_once 'MyWord.class.php';
import("Vendor.PHPExcel.Writer.Excel2007");
import("Vendor.PHPExcel.PHPExcel");
import("Vendor.PHPExcel.PHPExcel.IOFactory");

const EXCELERR = array
        (
            "FILE_NOTFOUND" => array('error' => 1,'message' => '文件不存在'),
            "FILE_CANTLOAD" => array('error' => 2,'message' => '文件加载失败'),
            "FILE_EMPTY" => array('error' => 3,'message' => '空文件不加载'),
            "FILE_IS_NOT_EXCEL" => array('error'=> 4,'message' => '非Excel文件'),
            "FILE_KIND_ERR" => array('error' => 5,'message' => '文件类型错误')
        );
class ExcelFile{
    // //待读取文件名
    // public $filename = null;
        //文件类型
    public $type = null;
    //私有构造函数，防止外界实例化对象
    public function __construct()
    {
       // $this->$filename = $file;
    }

    private function __clone()
    {

    }

    // public function getInstance($file)
    // {
    //     if (!empty($file) && is_file($file)) {
    //         if (!isset(self::$filename)) {
    //             $file = new self($file);
    //         }
    //         return $file;
    //     }
    //     return MYEXCEL_ERR['FILE_NOTFOUND'];
    // }
    /**
     * 获取文件内容
     */
    public function FileReader($filename)
    {
        if(empty($filename)){
            return EXCELERR['FILE_NOTFOUND'];
        }
        else if($this->isExcel($filename) === 'excel'){
            $path   = realpath('./') . '\tasktable\\' .$filename;
            $filename      = iconv("utf-8", "gb2312", $path);
            $inputFileType = \PHPExcel_IOFactory::identify($filename);
            $objReader     = \PHPExcel_IOFactory::createReader($inputFileType);
            try{
                $obj       = $objReader->load($filename); //全部加载文件
            }catch(Exception $e){
                return EXCELERR['FILE_CANTLOAD'];
            }
            $data          = $obj->getActiveSheet()->toArray();
            // var_dump($data);die;
            if($data[0][0] == null)
            {
                return EXCELERR['FILE_EMPTY'];
            }
            return $data;
        }elseif($this->isExcel($filename) === 'word'){
            $trueName = $filename;
            $tempName = session('gid')."temp_file.docx";
            copy(realpath('./') . "\\tasktable\\" . $filename ,realpath('./') . "\\temp_file\\" . $tempName);

            $myWord = MyWord::getInstance($tempName);
            $tables_array_data = $myWord->getTable(0);

            foreach ($tables_array_data as $key => $value) {
                foreach ($tables_array_data[$key] as $ke => $va) {
                    $tables_array_data[$key][$ke] =  $tables_array_data[$key][$ke]['text'];
                }	
            }

            return $tables_array_data;
        }else{
            return EXCELERR['FILE_IS_NOT_EXCEL'];
        }
    }
    /**
     *  将操作后的数据填入Excel表,直接覆盖原文件;
     */
    public function ExcelWriter($data = null,$filename = 'test.xlsx')
    {
        $bool = false;
        if($this->isExcel($filename) === 'excel'){
            try {
                $objPHPExcel = new \PHPExcel();
                $objSheet = $objPHPExcel->getActiveSheet();
                $objSheet->fromArray($data);
                $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
                $save_url = realpath('./').'\tasktable\\'.$filename;
                // echo $save_url;die;/
                $objWriter->save($save_url);  
                $bool = true;
                return $bool;
            } catch (Exception $e) {
                //echo $e;die;
            }
            return $bool;
        }elseif($this->isExcel($filename) === 'word'){
            $trueName = $filename;
            // echo $trueName;die;
            $tempName = session('gid')."temp_file.docx";
            copy(realpath('./') . "\\tasktable\\" . $trueName ,realpath('./') . "\\temp_file\\" . $tempName);

            $myWord = MyWord::getInstance($tempName);
            $bool = $myWord->setTableCell(0,$data);
            if($bool){
                $t = copy(realpath('./') . "\\temp_file\\" . $tempName,realpath('./') . "\\tasktable\\" . $trueName );
                // var_dump($t);
                $url = realpath('./')."\\temp_file\\" . $tempName;
                unlink($url);
            }else{
                echo "保存出错";die;
            }
            return $bool;
        }else{
            return EXCELERR['FILE_KIND_ERR'];
        }

    }
    private function isExcel($filename)
    {
        $end = explode('.',$filename,2);
        // var_dump($end[1]);die;
        if(strcmp($end[1],'xlsx') == 0 || strcmp($end[1],'xls') == 0)
        {
            return 'excel';
        }
        elseif(strcmp($end[1],'docx') == 0 || strcmp($end[1],'doc') == 0)
        {
            return 'word';
        }else{
            return false;
        }
    }


}
