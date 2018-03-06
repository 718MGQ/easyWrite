<?php 
	// $_FILES["file"]["name"] = iconv("utf-8","gb2312",$_FILES["file"]["name"]);
        $uptype = array("doc","docx","xlsx","xls");
        header('Content-type: text/html; charset=utf-8');
        $gid = 130001;
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
            // $this->ajaxReturn(
            //         array(
            //             "code"    => 1,
            //             "message" => $sp[$code],
            //             "filename"=>""
            //         )
            //     );
            echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
        }
        else
        {

            // header('HTTP/1.1 500 Internal Server Error');
            $torrent = explode(".", $_FILES["file"]["name"]);
            $fileend = end($torrent);
            $fileend = strtolower($fileend);
            if(!in_array($fileend, $uptype))
            //检查上传文件类型
            {
                // header('HTTP/1.1 500 Internal Server Error');
                // $this->ajaxReturn(
                //         array(
                //             "code"    => 1,
                //             "message" => "文件类型错误",
                //             "filename"=> ""
                //         )
                //     );
                    echo "文件类型错误";
                // exit;
            }
            if (file_exists(realpath('./') . "/tasktable/" . $_FILES["file"]["name"]))
            {
                echo $_FILES["file"]["name"] . " 已存在 ";
                // header('HTTP/1.1 500 Internal Server Error');
                // $this->ajaxReturn(
                //         array(
                //             "code"    => 1,
                //             "message" => "文件已存在",
                //             "filename"=> $gid.$_FILES["file"]["name"]
                //         )
                //     );
            }
            else
            {

                // header('HTTP/1.1 200 OK');
                move_uploaded_file($_FILES["file"]["tmp_name"],
                                    realpath('./') . "/tasktable/" . $gid.$_FILES["file"]["name"]);
                echo realpath('./') . "tasktable/" . $_FILES["file"]["name"]."上传成功";
                // $this->ajaxReturn(
                //         array(
                //             "code"    => 0,
                //             "message" => "上传成功",
                //             "filename"=> $gid.$_FILES["file"]["name"]
                //         )
                //     );
            }
        }
    }
