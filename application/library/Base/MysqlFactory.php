<?php namespace Base;

use Medoo;
use PDO;
use Yaf_Application;

Class MysqlFactory
{
    private static $read_instance;//读实例

    private static $write_instance;//写实例

    private static $test_instance;//测试实例

    /**
     * 获取数据库基本配置
     * @param $type
     * @return bool
     */
    private static function getDbConfig($type)
    {
        $config = Yaf_Application::app()->getConfig()->get('database');

        switch($type)
        {
            case 'main':
                return $config['main'];
                break;
            case 'slave':
                return $config['slave'];
                break;
            case 'test':
                return $config['test'];
            default:
                return false;
                break;
        }
    }

    /**
     * 获取测试实例连接
     * @param $database_name
     * @return Medoo
     */
    public static function getTestInstance($database_name)
    {
        $config = self::getDbConfig('test');
        return self::$test_instance = new Medoo([
            'database_type'=>$config['type'],
            'server'=>$config['hostname'],
            'database_name'=>$database_name,
            'username'=>$config['username'],
            'password'=>$config['password'],
            'port'=>3306,
            'charset'=>$config['charset'],
            'option'=>[
                PDO::ATTR_CASE => PDO::CASE_NATURAL
            ]
        ]);
    }

    /**
     * 读数据数据库链接
     * @param $database_name
     * @return Medoo
     */
    public static function getReadInstance($database_name)
    {
        $config = self::getDbConfig('slave');
        return self::$read_instance = new Medoo([
            'database_type'=>$config['type'],
            'server'=>$config['hostname'],
            'database_name'=>$database_name,
            'username'=>$config['username'],
            'password'=>$config['password'],
            'port'=>3306,
            'charset'=>$config['charset'],
            'option'=>[
                PDO::ATTR_CASE => PDO::CASE_NATURAL
            ]
        ]);
    }

    /**
     * 写数据数据库连接
     * @param $database_name
     * @return Medoo
     */
    public static function getWriteInstance($database_name)
    {
        $config = self::getDbConfig('main');
        return self::$write_instance = new Medoo([
            'database_type'=>$config['type'],
            'server'=>$config['hostname'],
            'database_name'=>$database_name,
            'username'=>$config['username'],
            'password'=>$config['password'],
            'port'=>3306,
            'charset'=>$config['charset'],
            'option'=>[
                PDO::ATTR_CASE => PDO::CASE_NATURAL
            ]
        ]);
    }
}