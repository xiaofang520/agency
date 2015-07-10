<?php

Class IndexController extends Base\BaseControllers
{
    public function init(){

        /* 首先关闭自动渲染 */
        Yaf_Dispatcher::getInstance()->disableView();
    }
    public function indexAction()
    {
        echo '这是admin模块中的indexControllers里的indexAction方法';

        return false;
    }
    public function testAction(){
        echo "Admin/index/test";
        $id=$_GET['id'];
        echo $id;
        return false;
    }
    public function goAction(){
        //echo "aa";

        /* 自己输出响应 */
       //∂ echo $this->render('moon');

       // $this->redirect('index?m=Admin&c=index&a=test');

      $this->display('User:moon',array('li'=>22));


    }
}