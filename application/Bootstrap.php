<?php

use DebugBar\StandardDebugBar;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;

/**
 * Class     Bootstrap
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用, 调用的次序和声明的次序相同
 * 这些方法, 都接受一个参数: Yaf_Dispatcher $dispatcher
 *
 */
class Bootstrap extends Yaf_Bootstrap_Abstract
{

    /**
     * Method  _initConfig
     * 初始化系统配置
     *
     *
     * @param Yaf_Dispatcher $dispatcher
     */
    public function _initConfig(Yaf_Dispatcher $dispatcher)
    {
        Yaf_Registry::set('config', Yaf_Application::app()->getConfig());
    }

    /**
     * Method  _initTimeZone
     * 初始化时区
     *
     *
     * @param \Yaf_Dispatcher $dispatcher
     */
    public function _initTimeZone(Yaf_Dispatcher $dispatcher)
    {
        date_default_timezone_set(Yaf_Registry::get('config')->application->default->timezone);
    }
    /**
     *初始化路由
     */
    public function _initRoute(Yaf_Dispatcher $dispatcher) {
        $router = Yaf_Dispatcher::getInstance()->getRouter();
        /**
         * 添加配置中的路由
         */
        $router->addConfig(Yaf_Registry::get("config")->routes);
    }
    /**
     * 读取全局方法
     *
     * @param Yaf_Dispatcher $dispatcher
     */
    public function _initGlobalFunctions(Yaf_Dispatcher $dispatcher)
    {
        $dir = scandir(APPLICATION_PATH . '/functions/');
        unset($dir[0]);
        unset($dir[1]);
        foreach ($dir as $value) {
            Yaf_Loader::import(APPLICATION_PATH . '/functions/' . $value);
        }
    }


    /**
     * 初始化插件
     */
    public function _initPlugins(Yaf_Dispatcher $dispatcher)
    {
        $global = new GlobalPlugin();
        $dispatcher->registerPlugin($global);
    }

    /**
     * Method  _initComposerAutoload
     *
     *
     * @param $dispatcher
     */
    public function _initComposerAutoload(Yaf_Dispatcher $dispatcher)
    {
        Yaf_Loader::import(APPLICATION_PATH . '/library/vendor/autoload.php');
    }

    /**
     * 仅在开发时开启
     *
     * @throws Exception
     */
    public function _initDebug()
    {

        $handler = PhpConsole\Handler::getInstance();
        $handler->start();
        $connector = PhpConsole\Connector::getInstance();
        $connector->setPassword('933271');

        // Configure eval provider
        $evalProvider = $connector->getEvalDispatcher()->getEvalProvider();
        $evalProvider->addSharedVar('post', $_POST); // so "return $post" code will return $_POST
        $evalProvider->setOpenBaseDirs(array(__DIR__)); // see http://php.net/open-basedir

        $connector->startEvalRequestsListener(); // must be called in the end of all configurations


    }
}