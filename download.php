<?php
	$file_name = $_GET["file_name"];
	// echo $file_name;
    $file_name = iconv("utf-8", "gb2312", $file_name);   
	$file_dir = realpath('./') . "/tasktable/";
    //$file_dir = chop($file_dir);//去掉路径中多余的空格
    $file_path = $file_dir.$file_name;
    //判断要下载的文件是否存在
    if(!file_exists($file_path))
    {
        echo '对不起,你要下载的文件不存在。';
        return false;
    }
    $file_size = filesize($file_path);
    header("Content-type: application/octet-stream");
    header("Accept-Ranges: bytes");
    header("Accept-Length: $file_size");
    header("Content-Disposition: attachment; filename=".substr($file_name, 6));
    readfile($file_path);
    if(file_exists($file_path))
	{
		unlink($file_path);
	}
    return true;
 ?>