<?php

/**
 * Created by fangjunhong.
 * User: xiaofang
 * Date: 15/7/6
 * Time: 上午10:40
 */
Class AgentController extends Base\BaseControllers
{
    public function init()
    {

        /* 首先关闭自动渲染 */
        //Yaf_Dispatcher::getInstance()->disableView();
    }

    public function loginAction()
    {
        if(!$this->checkUserCookie()){
            $this->getView()->display('common/headlogin.phtml');
            $this->getView()->display('login/login.phtml');
            $this->getView()->display('common/footlogin.phtml');
        }else{
            $this->redirect("index?m=agency&c=agent&a=index");

        }



    }

    /**
     * 验证cookie的值，来实现自动登录
     */
    public function checkUserCookie()
    {

        if (empty($_COOKIE['userid']) || empty($_COOKIE['username']) || empty($_COOKIE['blxsoft_sessionid']) || empty($_COOKIE['nickname']) || empty($_COOKIE['logined_token'])) {
            return false;
        } else {
            return true;
        }

    }

    public function getCookieAction()
    {
        var_dump($_COOKIE);
        return false;
    }
    public function deCookieAction()
    {
        setcookie('username',null);
        return false;
    }
    public function checkLoginAction()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $autologin = (int)$_POST['autologin'];

        $api_type = "user";
        $apiname = "login";

        $params_data = array(
            'username' => $username,
            'password' => $password,
            'login_method' => 'PC端',
        );


        $result = load_coresystem_api($api_type, $apiname, $params_data);
        $result['backurl'] = 'index.php?m=agency&c=agent&a=index';
        if ($result['status'] == 1) {
            if ($autologin == 1) {
                setcookie('userid', $result['data']['userid'], time()+3160000000);
                setcookie('username', $result['data']['username'], time()+3160000000);
                setcookie('blxsoft_sessionid', $result['data']['blxsoft_sessionid'], time()+3160000000);
                setcookie('nickname', $result['data']['nickname'], time()+3160000000);
                setcookie('logined_token', $result['data']['logined_token'], time()+3160000000);
            }else{
                setcookie('userid', $result['data']['userid']);
                setcookie('username', $result['data']['username']);
                setcookie('blxsoft_sessionid', $result['data']['blxsoft_sessionid']);
                setcookie('nickname', $result['data']['nickname']);
                setcookie('logined_token', $result['data']['logined_token']);
            }


        }
        echo json_encode($result);
        return false;

    }

    /**
     * 进入经销商中心
     * @return bool
     */
    public function indexAction()
    {
        $uid = $_COOKIE['userid'];
        $user = new AgentManagerModel('agency');
        $agent_manager = $user->_db->get_one("*", 'bbl_agent_manager', "userid=$uid and role !=3");
        $agenter_id = $agent_manager['agenter_id'];

        if (empty($agent_manager)) {
            echo "没有绑定经销商";
            exit();
        }
        $agent = $user->_db->get_one("*", 'bbl_agenter', "id=$agenter_id");
        if ($agent['isban'] == 1) {
            echo "访问受限,限制登陆";
            exit();
        }

        switch ($agent['status']) {
            case 0 :
                echo "审核中";
                exit();
                break;
            case 2 :
                echo "没有通过审核";
                exit();
                break;
            default :
                echo "审核通过";;
        };


        $this->getView()->assign('agent_manager', $agent_manager);
        $this->getView()->assign('uid', $uid);
        $this->getView()->assign('agent', $agent);


        $this->display('../agent/AgentInfo');

        return false;
    }

    public function showUsersAction()
    {
        var_dump($this->request);
        $pid = (int)$_GET['pid'];
        $uid = (int)$_GET['uid'];

        $user = new AgentManagerModel('agency');


        $agent_manager = $user->_db->get_one("*", 'bbl_agent_manager', "userid=$uid and role !=3");
        $agent = $user->_db->get_one("*", 'bbl_agenter', "id=$pid");

        $agent_users = $user->_db->select("*", 'bbl_agenter', "pid=$pid");
        $this->getView()->assign('pid', $pid);
        $this->getView()->assign('uid', $uid);
        $this->getView()->assign('agent', $agent);
        $this->getView()->assign('users', $agent_users);
        $this->getView()->assign('agent_manager', $agent_manager);
        $this->display('AgentUsers');

        return false;
    }

    /**
     * 添加二级经销商
     * @return bool
     */
    public function addAgentAction()
    {
        $uid = (int)$_GET['uid'];
        $pid = (int)$_GET['pid'];
        $infoid = (int)$_GET['infoid'];
        $info = '';
        switch ($infoid) {
            case 1:
                $info = '请认真填写信息';
                break;
            case 2:
                $info = '你输入的管理员账号已被占用';
                break;
            default:
                ;
        }


        $user = new AgentManagerModel('agency');
        $agent_manager = $user->_db->get_one("*", 'bbl_agent_manager', "userid=$uid and role !=3");

        $this->getView()->assign('title', '添加二级经销商');
        $this->getView()->assign('info', $info);
        $this->getView()->assign('pid', $pid);
        $this->getView()->assign('uid', $uid);
        $agent = $user->_db->get_one("*", 'bbl_agenter', "id=$pid");
        $this->getView()->assign('agent_manager', $agent_manager);
        $this->getView()->assign('agent', $agent);
        $this->getView()->display('common/head.phtml');
        $this->display('addAgent');
        return false;
    }

    /**
     * 保存添加的代理商信息
     * @return bool
     */
    public function saveAddAgentAction()
    {

        $uid = (int)$_POST['uid'];//当前用户登陆的uid
        $pid = (int)$_POST['pid'];
        $agentname = addslashes(ltrim(rtrim($_POST['agentname'])));
        $userid = (int)$_POST['userid'];
        $truename = addslashes(ltrim(rtrim($_POST['truename'])));
        $content = addslashes(ltrim(rtrim($_POST['content'])));
        //先判断userid 有没有绑定其他经销商
        $user = new AgentManagerModel('agency');

        $is_agent = $user->_db->get_one('*', 'bbl_agent_manager', "userid=$userid");

        if (!empty($is_agent)) {
            echo "is agenter,please exchange user";

            $this->redirect("index?m=agency&c=agent&a=addAgent&uid=$uid&pid=$pid&infoid=2");
            exit();
        }
        $agent = array(
            'pid' => $pid,
            'agentname' => $agentname,
            'addtime' => time(),
            'aid' => 0,
            'adminer_truename' => '',
            'userid' => $userid,
            'truename' => $truename,
            'content' => $content,
            'status' => 0
        );
        $agenter_id = $user->_db->insert($agent, 'bbl_agenter', true);

        $agent_manager = array(
            'userid' => $userid,
            'agenter_id' => $agenter_id,
            'truename' => $truename,
            'addtime' => time(),
            'role' => 2,
        );
        $a = $user->_db->insert($agent_manager, 'bbl_agent_manager');
        $this->redirect("index?m=agency&c=agent&a=showUsers&pid=$pid&uid=$uid");
        return false;
    }

    /**
     * show管理员
     * @return bool
     */
    public function showAgentManagersAction()
    {


        $id = (int)$_GET['pid'];
        $uid = (int)$_GET['uid'];
        $this->showTopAction($uid, $id);
        $user = new AgentManagerModel('agency');
        $agent_main_manager = $user->_db->get_one("*", 'bbl_agent_manager', "agenter_id=$id and role =1 ");
        $agent_managers = $user->_db->select("*", 'bbl_agent_manager', "agenter_id=$id and role !=1");


        $this->getView()->assign('main_manager', $agent_main_manager);
        $this->getView()->assign('managers', $agent_managers);
        $this->getView()->assign('title', '我的员工！');

        $this->getView()->display('common/head.phtml');
        $this->getView()->display('agent/AgentManagers.phtml');
        return false;
    }

    /**
     * 变更管理员角色
     * @return bool
     */
    public function updateManagerRoleAction()
    {
        $userid = (int)$_GET['userid'];
        $role = (int)$_GET['role'];
        $uid = (int)$_GET['uid'];
        $pid = (int)$_GET['pid'];

        $this->showTopAction($uid, $pid);
        $user = new AgentManagerModel('agency');
        $data = array(
            'role' => $role
        );
        $user->_db->update($data, 'bbl_agent_manager', "userid=$userid");

        $this->redirect("index?m=agency&c=agent&a=showAgentManagers&pid=$pid&uid=$uid");
        return false;
    }

    /**
     *
     * @return bool
     */

    public function addAgentManagerAction()
    {
        $uid = (int)$_GET['uid'];
        $pid = (int)$_GET['pid'];
        $infoid = (int)$_GET['infoid'];
        $info = '';
        switch ($infoid) {
            case 1:
                $info = '请认真填写信息';
                break;
            case 2:
                $info = '你输入的员工账号已被占用';
                break;
            default:
                ;
        }
        $this->showTopAction($uid, $pid);
        $this->getView()->assign("title", '添加员工');
        $this->getView()->assign("info", $info);
        $this->getView()->display("common/head.phtml");
        $this->getView()->display("agent/addAgentManager.phtml");

        return false;
    }

    /**
     * 判断userid 存不存在或者是否已被占用，返回json数据
     * @return bool
     */
    public function checkUseridAction()
    {
        $userid = (int)$_POST['userid'];
        $user = new AgentManagerModel('agency');
        $agent_manager = $user->_db->get_one("*", 'bbl_agent_manager', "userid=$userid and role !=3");
        $info = array();

        if (empty($agent_manager)) {
            $info['status'] = 1;
            $info['info'] = '可以注册';
        } else {
            $info['status'] = 2;
            $info['info'] = '该用户已被占用';
        }
        echo json_encode($info);

        return false;
    }

    public function checkUseridInAction()
    {
        $userid = (int)$_POST['userid'];
        $user = new AgentManagerModel('agency');
        $agent_manager = $user->_db->get_one("*", 'bbl_agent_manager', "userid=$userid and role !=3");
        $info = array();

        if (empty($agent_manager)) {
            return true;
        } else {
            return false;
        }


    }

    /**
     * 保存添加的信息
     * @return bool;
     */
    public function saveAddAgentManagerAction()
    {
        $uid = (int)$_POST['uid'];
        $pid = (int)$_POST['pid'];
        $userid = (int)$_POST['userid'];
        $truename = addslashes(ltrim(rtrim($_POST['truename'])));
        if ($this->checkUseridInAction()) {
            $data = array(
                'userid' => $userid,
                'addtime' => time(),
                'agenter_id' => $pid,
                'role' => 2,
                'truename' => $truename,
            );

            $user = new AgentManagerModel('agency');
            $user->_db->insert($data, 'bbl_agent_manager');
            $this->redirect("index?m=agency&c=agent&a=showAgentManagers&pid=$pid&uid=$uid");


        } else {
            $this->redirect("index?m=agency&c=agent&a=addAgentManager&pid=$pid&uid=$uid&infoid=2");


        }
        return false;
    }

    /**
     * 测试方法
     * @return bool
     */
    public function testAction()
    {


        /*$this->getView()->display('common/headlogin.phtml');
        $this->getView()->display('login/login.phtml');
        $this->getView()->display('common/footlogin.phtml');*/
        $api_type = "user";
        $apiname = "login";
        $username = '毛主席夸我帅';
        $password = '8888168';
        $params_data = array(
            'username' => $username,
            'password' => $password,
            'login_method' => 'PC端',
        );

        $a = load_coresystem_api($api_type, $apiname, $params_data);

        var_dump($a);
        //获取单用户信息
        /*$userid = 3735;
        $api_type = "user";
        $apiname = "get_everyone_userinfo";
        $params_data = array(
            'userid' => $userid
        );
        $a = load_coresystem_api($api_type, $apiname, $params_data);
        var_dump($a);*/
        return false;

    }


    /**
     *封装头部文件信息
     * @param $uid
     * @param $id
     */
    public function showTopAction($uid, $id)
    {
        $user = new AgentManagerModel('agency');
        $agent_manager = $user->_db->get_one("*", 'bbl_agent_manager', "userid=$uid and role !=3");

        $this->getView()->assign('pid', $id);
        $this->getView()->assign('uid', $uid);
        $agent = $user->_db->get_one("*", 'bbl_agenter', "id=$id");
        $this->getView()->assign('agent_manager', $agent_manager);
        $this->getView()->assign('agent', $agent);

    }


    /**
     * 调用核心系统中间部件API共用接口
     * @调用案例： load_coresystem_api($api_type,$apiname,$params_data)
     * @param $api_type api类别
     * @param $apiname 调用api名称
     * @param $params_data array 传递参数
     * @申明：类名称规范为$classname.class.php
     * @调用示例：load_coresystem_api("email","register",array());
     */
    public function load_coresystem_api($api_type, $apiname, $params_data)
    {
        //构建传递参数
        $rand_num = rand(1, 10000000);
        $api = Yaf_Application::app()->getConfig()->get('api');
        $api_path = $api['url'] . "?operate=$api_type&deposit=$apiname";
        $core_system_api_communication_key = Yaf_Application::app()->getConfig()->get('api')['key'];
        $params = serialize($params_data);
        $time = time();
        $num = $rand_num ^ 0x32;
        $token = md5($time . $api_type . $apiname . $core_system_api_communication_key . $rand_num . $params);
        $params = urlencode($params);
        $params_str = "params=$params&t=$time&num=$num&token=$token";
        //模拟post操作提交到api服务器


        $result = \Core\Post::makeRequest($api_path, $params_str, "");
        if ($result['result']) {
            return json_decode($result['msg'], true);
        } else {
            echo "POST调用系统API失败，请联系开发工程师";
            exit;
        }
    }


}