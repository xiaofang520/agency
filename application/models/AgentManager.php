<?php

/**
 * Created by PhpStorm.
 * User: xiaofang
 * Date: 15/6/25
 * Time: 下午10:01
 */
class AgentManagerModel extends model\Model
{
    public $database = 'agency';
    public function test()
    {
        echo "test";
    }

    public function select()
    {
        header("Content-type:text/html;charset:utf-8");
        $sql = 'select * from bbl_agenter';
        var_dump($this->_db->query_select($sql));
        echo "<hr>";
       var_dump($this->_db ->select('*', "bbl_agenter", '', '',  '', '', ''));

    }
}