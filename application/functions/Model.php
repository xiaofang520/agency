<?php
/**
 * Created by PhpStorm.
 * User: xiaofang
 * Date: 15/6/26
 * Time: 上午11:10
 */

namespace model;


class Model  {
    public $_db;
    public $database;
    public function __construct($db=''){

        $this->database=$db == '' ? $this->database: $db;
        $this->_db=new \mysql();
        $this->_db->open(getSqlConfig($this->database));
    }


} 