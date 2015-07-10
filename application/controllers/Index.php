<?php
use Db\Moon;

/**
 *
 */
class IndexController extends Base\BaseControllers
{

    /**
     * Method  indexAction
     *
     */
    public function indexAction()
    {
        $log = new Monolog\Logger('name');
        $log->pushHandler(new Monolog\Handler\StreamHandler(APPLICATION_PATH . '/logs/app.log', Monolog\Logger::WARNING));

        $log->addWarning('测试', array(123123));
    }

    public function testAction()
    {

        $conn = new mysql();
        $conn->open(getSqlConfig('main'));

        $sql = "select * from userinfo";
        $result = $conn->query_select($sql);
        var_dump($result);
        $r1 = $conn->query($sql);


        echo Carbon\Carbon::today();

        return false;
    }











}