<?php 

class file
{

	private $basedir = '/var/www/html';
	private $userbasedir;
	private $loc;

	public function __construct($loc)
	{
		$_SESSION['userbase'] = '/wbj';
		if(isset($_SESSION['userbase']))
		{
			$this->userbasedir = $this->basedir . $_SESSION['userbase'];
			$this->loc = $this->$userbasedir . $loc;
		}
		else
		{
			return null;
		}
	}

	public function download($filename)
	{
		$filename = explode('/', $filename);
		if(count($filename) === 1)
		{
			$filename = $filename[0];
			if (is_file($this->loc . $filename)) {
				$file = $this->loc . $filename;
				header('Content-Type: application/octet-stream');
				header('Content-Disposition:attachment;filename=' . $filename);
				header('Content-Length: ' . filesize($file));
				@readfile($file);
			}
			elseif(is_dir($this->loc . $filename))
			{
				$zipfile = $this->userbasedir . '/' . $filename . '.zip';
				// 使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释
				$zip = new ZipArchive (); 

				if ($zip->open ($zipfile, ZIPARCHIVE::CREATE) !== TRUE)
				{
				    return 0;
				}
				$this->zip($zip, $filename);
				$zip->close (); // 关闭
				//下面是输出下载;
				header ("Cache-Control: max-age=0");
				header ("Content-Description: File Transfer");
				header ('Content-disposition: attachment; filename=' . basename ($zipfile)); // 文件名
				header ("Content-Type: application/zip"); // zip格式的
				header ("Content-Transfer-Encoding: binary"); // 告诉浏览器，这是二进制文件
				@readfile($zipfile);//输出文件;
				unlink($zipfile);
			}
		}
		else
		{
				$zipfile = $this->userbasedir . '/zip.zip';
				// 使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释
				$zip = new ZipArchive (); 
				if ($zip->open ($zipfile, ZIPARCHIVE::CREATE) !== TRUE)
				{
				    return 0;
				}
				for($i = 0, $len = count($filename); $i < $len; $i++)
				{
					$this->zip($zip, $filename[$i]);
				}
				$zip->close (); // 关闭
				//下面是输出下载;
				header ("Cache-Control: max-age=0");
				header ("Content-Description: File Transfer");
				header ('Content-disposition: attachment; filename=' . basename ($zipfile)); // 文件名
				header ("Content-Type: application/zip"); // zip格式的
				header ("Content-Transfer-Encoding: binary"); // 告诉浏览器，这是二进制文件
				@readfile($zipfile);//输出文件;
				unlink($zipfile);
		}
	}

	public function zip($zip, $path)
	{
		if(is_dir($this->userbasedir . '/' . $path))
		{
		    $handler=opendir($this->userbasedir . '/' . $path); //打开当前文件夹由$path指定。
		    while(($filename=readdir($handler))!==false){
		        if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
		            if(is_dir($this->userbasedir . '/' . $path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
		                $this->zip($zip, $path."/".$filename);
		            }else{ //将文件加入zip对象
		                $zip->addFile($this->userbasedir . '/' . $path."/".$filename, $path."/".$filename);
		            }
		        }
		    }
		}
		else
		{
			$zip->addFile($this->userbasedir . '/' . $path, $path);
		}
	    @closedir($this->userbasedir . '/' . $path);
	}

}
	public function uploadFile() {
		if($_FILES != null) {
			$_FILES = $this->reArrayFiles($_FILES['fileUp']);
			$file = array();
			for($i = 0; $i < count($_FILES); $i++) {
				$time = date("YmdHisa");
				$fileName = $_FILES[$i]['name'];
				$fileAdd = "upload/".$time.$_SESSION['userID'].$fileName;
				move_uploaded_file($_FILES[$i]['tmp_name'], $fileAdd);
				$path = "upload";
				$handle = opendir($path);
				while(($item = readdir($handle)) !== false) {
					if($item == $time.$_SESSION['userID'].$fileName) {
						$file[$i]['fileName'] = $fileName;  
						$file[$i]['fileAdd'] = $fileAdd;
						$file[$i]['fileSize'] = $_FILES[$i]['size'];
						break;
					}
				}
				if(!isset($file[$i]['fileName'])) {
					$file[$i]['fileName'] = '1';
					$file[$i]['fileAdd'] = '1';
				}
				sleep(1);
				$file[$i]['error'] = $_FILES[$i]['error'];
			}
			//echo json_encode($file);
		}
	}

	private function reArrayFiles(&$file_post) {
	    $file_ary = array();
	    $file_count = count($file_post['name']);
	    $file_keys = array_keys($file_post);

	    for ($i=0; $i<$file_count; $i++) {
	        foreach ($file_keys as $key) {
	            $file_ary[$i][$key] = $file_post[$key][$i];
	        }
	    }

	    return $file_ary;
	}

