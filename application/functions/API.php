<?php

/**
 * 调用核心系统中间部件API共用接口
 * @调用案例： load_coresystem_api($api_type,$apiname,$param)
 * @param $api_type api类别
 * @param $apiname 调用api名称
 * @param $param array 传递参数
 * @return 返回json格式数据格式
 * @调用示例：load_coresystem_api("email","register",array());
 */


function load_coresystem_api($api_type,$apiname,$params_data){
    //构建传递参数
    $rand_num=rand(1,10000000);
    $api=Yaf_Application::app()->getConfig()->get('api');
    $api_path=$api['url']."?operate=$api_type&deposit=$apiname";
    $core_system_api_communication_key=Yaf_Application::app()->getConfig()->get('api')['key'];
    $params=serialize($params_data);
    $time=time();
    $num=$rand_num^0x32;
    $token=md5($time.$api_type.$apiname.$core_system_api_communication_key.$rand_num.$params);
    $params=urlencode($params);
    $params_str="params=$params&t=$time&num=$num&token=$token";
    //模拟post操作提交到api服务器


    $result=\Core\Post::makeRequest($api_path,$params_str,"");
    if($result['result']){
        return json_decode($result['msg'],true);
    }else{
        echo "POST调用系统API失败，请联系开发工程师";
        exit;
    }
}


?>