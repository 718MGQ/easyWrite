<?php
namespace Common\Model;

use Think\Model;

class AllcolumnsModel extends Model
{

    protected $tableName = 'all_columns';
    /**
     * 获取文件列对应的字段名
     *
     */
    /*$tableinfo = Array(
    [0] => Array
    (
    [0] => 序号
    [1] => 名称
    [2] => 批准单位
    [3] => 证书编号
    [4] => 证书编号
    [5] => 批准时间
    [6] => 类别
    [7] => 得分
    )

    [1] => Array
    (
    [0] => 1
    )

    [2] => Array
    (
    [0] => 2
    )
    )
     */
    //数据表id&&name,excel->toArray数组
    public function getColumns($table, $tableinfo)
    {

        // $sql = "SELECT columns_name,keywords from all_columns where all_table_id = %s";
        // $data = $this->query($sql,$table['id']); //获取从all_columns中$table['id']的字段名称与关键字
        // // var_dump($data);die;
        // $columns = array();
        // $e = 0;
        // for ($i = 0; $i < count($tableinfo[0]); $i++) {

        //     if ($tableinfo[0][$i] == '序号') {
        //         continue;
        //     }
        //     $cishu = 1;
        //     for ($j = 0; $j < count($data); $j++) {

        //         if (strstr($tableinfo[0][$i], $data[$j]['keywords']) === false) {
        //             $num[$tableinfo[0][$i]] = $cishu++;
        //         }
        //     }
        //     $e++;
        // }
        //     $count = count($data);
        //     for ($i = 1, $x = 0; $i < count($tableinfo[0]); $i++) {
        //         if ($num[$tableinfo[0][$i]] == $count) {
        //             $temp[$x]['tempcolumn'] = 'temp' . $i;
        //             $temp[$x]['keywords']   = $tableinfo[0][$i];
        //             $x++;
        //         }
        //     }

        //     // var_dump($temp);
        //     // die;
        //     /*如果Excel表中没有相对应的字段就添加all_columns，增加$table['table_name'] */
        //     if ($temp != null) {
        //         $insertsql = 'INSERT INTO all_columns (all_table_id,columns_name,keywords) VALUES (';
        //         $fieldsql  = 'ALTER TABLE ' . $table['table_name'] . '  ADD column ';
        //         for ($i = 0, $s = count($temp); $i < $s; $i++) {
        //             if ($i < ($s - 1)) {
        //                 $fieldsql = $fieldsql . $temp[$i]['tempcolumn'] . ' varchar(100) not null default ""' . ', ADD ';

        //                 $insertsql = $insertsql . '\'' . $table['id'] . '\'' . ',' . '\'' . $temp[$i]['tempcolumn'] . '\'' . ',' . '\'' . $temp[$i]['keywords'] . '\'' . '),(';
        //             } else {
        //                 $fieldsql = $fieldsql . $temp[$i]['tempcolumn'] . ' varchar(100) not null default ""';

        //                 $insertsql = $insertsql . '\'' . $table['id'] . '\'' . ',' . '\'' . $temp[$i]['tempcolumn'] . '\'' . ',' . '\'' . $temp[$i]['keywords'] . '\'' . ')';
        //             }
        //         }
        //         try{
        //             $this->execute($fieldsql);
        //         }catch(Exception $e)
        //         {
        //             return false;
        //         }
        //         try{
        //             $this->execute($insertsql);
        //         }catch(Exception $e)
        //         {
        //             return false;
        //         }
        //     }

            $sql = "SELECT columns_name,keywords from all_columns where all_table_id = %s";
            $data = $this->query($sql,$table['id']);
            for ($i = 0, $e = 0; $i < count($tableinfo[0]); $i++) {
                if ($tableinfo[0][$i] == '序号') {
                    continue;
                }
                for ($j = 0; $j < count($data); $j++) {
                    if (strstr($tableinfo[0][$i], $data[$j]['keywords']) != false) {
                        $columns[$e][] = $data[$j]['columns_name'];
                        $columns[$e][] = $i;
                        $e++;
                    }
                }
            }
            // print_r($columns);die;
            return $columns;
    }
    /*
    Array
    (
    [0] => Array
    (
    [0] => unit
    [1] => 2
    )

    [1] => Array
    (
    [0] => certificateid
    [1] => 4
    )
    )
     */
    public function getColumnsContents($gid, $columns, $tablename)
    {
        $sum = count($columns);
        $sql = 'SELECT ';
        for ($i = 0; $i < $sum; $i++) {
            if ($i < ($sum - 1)) {
                $sql = $sql . $columns[$i][0] . ',';
            } else {
                $sql = $sql . $columns[$i][0];
            }
        }
        $sql = $sql . ' FROM ' . $tablename . ' WHERE gid = ' . $gid;

        $data          = $this->query($sql);
        $num           = count($data);
        $data          = $this->stringIndexToNum($data);
        $data['count'] = $num;
        return $data;
    }
    /**
     * 获取数据库tablecategory的ID
     * @param  [type] $category [description]
     * @return [type]           [description]
     */
    public function getId($category)
    {
        // $category = '国家自然科学技术发明一等奖';
        $getTablesql = 'SELECT b.table_name from all_columns a,all_table b WHERE a.keywords = \'' . $category . '\' AND a.all_table_id = b.id';
        // echo $getTablesql;
        try{
            $tablename = $this->query($getTablesql);
            $tablename = $tablename[0]['table_name'];
            $tablename = 'resultpropertycategory';
            $getIdsql  = 'SELECT id FROM `' . $tablename . '` WHERE category = "' . $category . '"';
            $idDate    = $this->query($getIdsql);
        }catch(Exception $e)
        {
            $idDate[0]['id'] = 1;
        }

        return $idDate[0]['id'];
    }
    public function getCategoryName()
    {

    }
    public function stringIndexToNum($data)
    {
        $a = 0;
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $array[$a] = $v;
                    $a++;
                }
            }
            $a          = 0;
            $data[$key] = $array;
        }
        return $data;
    }
}
