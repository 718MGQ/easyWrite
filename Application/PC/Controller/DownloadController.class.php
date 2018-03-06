<?php
namespace PC\Controller;

use Think\Controller;

class DownloadController extends Controller
{
    public function index()
    {
        $gid = session('gid');
        $file_name = $_GET["file_name"];
       // $file_name = iconv("utf-8","gb2312",$file_name);
        $file_dir = realpath('./') . "\\tasktable\\";
        //$file_dir = chop($file_dir);//去掉路径中多余的空格
        $file_path = $file_dir.$gid.$file_name;
        // die($file_path);
        //判断要下载的文件是否存在
        if(!file_exists($file_path))
        {
            $this->ajaxReturn(
                    array(
                        'code' => 1,
                        'message' => '文件不存在'
                    )
            );
            // echo '对不起,你要下载的文件不存在。';
            // return false;
        }
        $file_size = filesize($file_path);
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length: $file_size");
        header("Content-Disposition: attachment; filename=".$file_name);
        ob_clean();flush();
        readfile($file_path);
        if(file_exists($file_path))
        {
            unlink($file_path);
            $task = M("task");
            $task->where('upfile_gid = '.$gid.' and file_name = \''.$_GET["file_name"].'\'')->delete();
        }
        //return true;
    }
}