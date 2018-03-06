<?php 
include 'MyWord.class.php';
Header("Content-type: text/html;charset=utf-8");

//实例化MyWord类
$myWord = MyWord::getInstance('sample.docx');

//获取全部表格
$tables = $myWord->getTables();
if( ! empty($tables))
{
	for ($i = 0, $len = count($tables); $i < $len; $i++)
	{ 
		echo arr_to_table($tables[$i]);
		echo '<hr>';
	}
}

//获取某个表格
$table = $myWord->getTable(0);
if( ! empty($tables))
{
	echo arr_to_table($table);
	echo '<hr>';
}

//获取某个表格的某个单元格
$cell = $myWord->getTableCell(0, 1, 2);
if( ! empty($cell))
{
	echo $cell['text'];
}


//---------other  functions-------------
//---------just ignore them-------------

function html_escape($arr)
{
	if(is_array($arr))
	{
		foreach ($arr as $value) {
			$value = html_escape($value);
		}
		return $arr;
	}
	else
	{
		return htmlspecialchars($arr);
	}
}

function arr_to_table($arr)
{
	$str = '<table>';
	for($i = 0, $len = count($arr[$i]); $i < $len; $i++)
	{
		$str .= '<tr>';
		for($j = 0, $lenj = count($arr[$i]); $j < $lenj; $j++)
		{
			if(empty($arr[$i][$j]))
			{
				$str .= '<td style="background-color:gray"></td>';
				continue;
			}
			try
			{
				$str .= "<td style='width:{$arr[$i][$j]['width']}px;height:{$arr[$i][$j]['height']}px; background-color:lightgray'>{$arr[$i][$j]['text']}</td>";
			}
			catch(Exception $e)
			{
				var_dump($arr[$i][$j]);
				var_dump($e);
				return 'error';
			}
		}
		$str .= '</tr>';
	}
	$str .= '</table>';
	return $str;
}
//--------------------------------------