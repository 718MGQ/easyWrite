<?php 
$file_name = $_POST["test"];
echo $file_name;
if(file_exists("./tasktable/".$file_name))
{
	unlink("./tasktable/".$file_name);
	echo "./tasktable/".$file_name."删除成功";
}