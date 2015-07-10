<?php namespace Base;

use Yaf_Controller_Abstract;

/**
 * Class     BaseControllers
 * 全局的控制器基类
 *
 */
class BaseControllers extends Yaf_Controller_Abstract {

    /**
     * Variable  request
     * request对象
     *
     * @var      object
     */
    public $request = null;

    /**
     * Variable  view
     * view对象
     *
     * @var      object
     */
    public $view = null;

    /**
     * Method  init
     * 初始化方法
     *
     */
    public function init() {
        $this->request = filterStr($this->getRequest()->getParams());

        $this->view    = $this->getView();
    }
    

}