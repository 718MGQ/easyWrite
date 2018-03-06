<?php 

const MYWORD_ERR = array(
	'LOAD_ERROR' => array(
		'errno'  => 1,
		'errmsg' => '无法加载com组件，请检查word版本及php是否开启该服务'
	),
	'FILE_NOTFOUND' => array(
		'errno'  => 2,
		'errmsg' => '待打开文件不存在'
	),
	'DOC_NOTFOUND' => array(
		'errno'  => 3,
		'errmsg' => '当前word文档不可用或不存在'
	),
	'TABLE_NOTEXIST' => array(
		'errno'  => 4,
		'errmsg' => '所选定的表格不存在'
	)
);

/**
 * @name 	Com操作word文档
 * @author  谷田 gutian@nefuer.net
 * @version 1.0 17/10/25
 */
class MyWord
{

    //优化单例模式
    private static $_instance = [];
	public $word;	        //实例化的com组件存储变量
	public $doc;			//当前操作文件
    private $filename;


    //私有构造函数，防止外界实例化对象
    private function __construct($filename)
    {
		$this->word = new COM('word.application');
        $this->open($filename);
    }
    //私有克隆函数，防止外部克隆对象
    private function __clone() {}

    /**
     * 优化单例模式创建方法
     * @param  string $filename word文档名称
     * @return MyWord|array     本类|错误信息
     */
    static public function getInstance($filename)
    {
        if( ! empty($filename) && is_file($filename))
        {
            if ( ! isset(self::$_instance[$filename]))
            {
    	    	if ( ! class_exists('COM')) {
    	    		return MYWORD_ERR['LOAD_ERROR'];
    	    	}
                self::$_instance[$filename] = new self($filename);
            }
            //返回实例，不可放入上方的内层循环，不可加else
            return self::$_instance[$filename];
        }
        return MYWORD_ERR['FILE_NOTFOUND'];
    }

   	/**
   	 * 打开word文档
   	 * @param  string $filename 文档名称
   	 * @return MyDoc|array      当前文档|错误信息
   	 */
    private function open(string $filename)
    {
    	$filename = realpath($filename);
    	if(is_file($filename))
    	{
    		$this->doc = new MyDoc($this->word, $filename);
            $this->filename = $filename;
        	return $this->doc;
    	}
    	else
    	{
    		return MYWORD_ERR['FILE_NOTFOUND'];
    	}
    }

    /**
     * 获取文档内所有表格
     * @param  MyDoc  $doc   	文档类，不填使用当前活动文档
     * @param  array  $iconv 	格式转换，如果传空数组则不进行装换
     * @return [type]      		[description]
     */
    public function getTables(MyDoc $doc = NULL, array $iconv = ['gbk', 'utf-8'])
    {
    	if(is_null($doc))
    	{
    		if($this->doc instanceof MyDoc)
    		{
    			$doc = $this->doc;
    		}
    		else
    		{
    			return MYWORD_ERR['DOC_NOTFOUND'];
    		}
    	}
    	return $doc->getTables($iconv);
    }

    /**
     * 获取文档内所有表格
     * @param  int    $num 		第%d个表格
     * @param  MyDoc  $doc   	文档类，不填使用当前活动文档
     * @param  array  $iconv 	格式转换，如果传空数组则不进行装换
     * @return [type]      		[description]
     */
    public function getTable(int $num, MyDoc $doc = NULL, array $iconv = ['gbk', 'utf-8'])
    {
    	if(is_null($doc))
    	{
    		if($this->doc instanceof MyDoc)
    		{
    			$doc = $this->doc;
    		}
    		else
    		{
    			return MYWORD_ERR['DOC_NOTFOUND'];
    		}
    	}
    	return $doc->getTable($num, $iconv);
    }

    /**
     * 获取文档内所有表格
     * @param  int    $num      第%d个表格
     * @param  int    $i        第%d行
     * @param  int    $j        第%d列
     * @param  MyDoc  $doc      文档类，不填使用当前活动文档
     * @param  array  $iconv    格式转换，如果传空数组则不进行装换
     * @return [type]           [description]
     */
    public function getTableCell(int $num, int $i, int $j, MyDoc $doc = NULL, array $iconv = ['gbk', 'utf-8'])
    {
        if(is_null($doc))
        {
            if($this->doc instanceof MyDoc)
            {
                $doc = $this->doc;
            }
            else
            {
                return MYWORD_ERR['DOC_NOTFOUND'];
            }
        }
        return $doc->getTableCell($num, $i, $j, ['gbk', 'utf-8']);
    }


    public function __destruct()
    {
        $this->word->Quit(false);
    }

}

class MyDoc
{

	public $doc;

	/**
	 * 打开word文档
	 * @param Com    $myWord   [description]
	 * @param string $filename [description]
	 */
	function __construct(Com $word, string $filename)
	{
        $word->Visible = 0;
		$word->Documents->Open($filename);
		$this->doc = $word->ActiveDocument;
	}

    public function getTables($iconv)
    {
    	$doc = $this->doc;
    	$tables = array();
		for($i = 1, $len = $doc->Tables->count; $i <= $len; $i++)
		{
			$nowTable = $doc->Tables->Item($i);
			$table = [];
			for($j = 1, $lenj = $nowTable->Rows->Count; $j < $lenj; $j ++)
			{
				$row = [];
				for($k = 1, $lenk = $nowTable->Columns->count; $k <= $lenk; $k++)
				{
                    try{
                        $width = $nowTable->Cell($j, $k)->Width;
                        $height = $nowTable->Cell($j, $k)->Height;
                        $text = $nowTable->Cell($j, $k)->Range->Text;
                        $text = substr($text, 0, strlen($text) - 2);
                        if( ! empty($iconv))
                        {
                            $text = iconv('gbk', 'utf-8', $text);
                        }
                        $row[] = array('text' => $text, 'width' => $width, 'height' => $height > 100 ? 100 : $height);
                    }
                    catch(Exception $e)
                    {
                        $row[] = [];
                    }
				}
				$table[] = $row;
			}
			$tables[] = $table;
		}
		return $tables;
    }

    public function getTable($num, $iconv)
    {
    	$doc = $this->doc;
    	$num = $num + 1;
    	if($doc->Tables->count < $num)
    	{
    		return MYWORD_ERR['TABLE_NOTEXIST'];
    	}
		$nowTable = $doc->Tables->Item($num);
		$table = [];
		for($j = 1, $lenj = $nowTable->Rows->Count; $j < $lenj; $j ++)
		{
			$row = [];
			for($k = 1, $lenk = $nowTable->Columns->count; $k <= $lenk; $k++)
			{
                try{
                    $width = $nowTable->Cell($j, $k)->Width;
                    $height = $nowTable->Cell($j, $k)->Height;
                    $text = $nowTable->Cell($j, $k)->Range->Text;
                    $text = substr($text, 0, strlen($text) - 2);
                    if( ! empty($iconv))
                    {
                        $text = iconv('gbk', 'utf-8', $text);
                    }
                    $row[] = array('text' => $text, 'width' => $width, 'height' => $height > 100 ? 100 : $height);
                }
                catch(Exception $e)
                {
                    $row[] = [];
                }
			}
			$table[] = $row;
		}
		return $table;
    }

    /**
     * 获取文档内所有表格
     * @param  int    $num      第%d个表格
     * @param  int    $i        第%d行
     * @param  int    $j        第%d列
     * @param  MyDoc  $doc      文档类，不填使用当前活动文档
     * @param  array  $iconv    格式转换，如果传空数组则不进行装换
     * @return [type]           [description]
     */
    public function getTableCell(int $num, int $j, int $k, array $iconv = ['gbk', 'utf-8'])
    {
        $doc = $this->doc;
        $num++;
        $j++;
        $k++;
        if($doc->Tables->count < $num)
        {
            return MYWORD_ERR['TABLE_NOTEXIST'];
        }
        $nowTable = $doc->Tables->Item($num);
        try{
            $width = $nowTable->Cell($j, $k)->Width;
            $height = $nowTable->Cell($j, $k)->Height;
            $text = $nowTable->Cell($j, $k)->Range->Text;
            $text = substr($text, 0, strlen($text) - 2);
            if( ! empty($iconv))
            {
                $text = iconv('gbk', 'utf-8', $text);
            }
            return array('text' => $text, 'width' => $width, 'height' => $height > 100 ? 100 : $height);
        }
        catch(Exception $e)
        {
            return [];
        }
    }

}
