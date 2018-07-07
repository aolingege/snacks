<?php
/**
 * Created by PhpStorm.
 * User: fangaolin
 * Date: 2018/4/24
 * Time: 11:52
 */

return [
    //无登录限制控制器列表
    'UNLIMITED_CONTROLLER' => array(),
    //无权限控制控制器与方法列表
    'UNAUTHED_CONTROLLER' => array(
        'Admin'=>array('checklogin','outlogin','mapping404'),
    ),
];
