<?php
/**
 * Created by PhpStorm.
 * User: fangaolin
 * Date: 2018/4/26
 * Time: 16:10
 */

namespace app\admin\controller;


class Index extends Admin
{
    /**管理首页
     * @return mixed
     */
    public function index()
    {
        $computer = $_SERVER['SERVER_SOFTWARE'];
        $computer = explode(' ',$computer);
        foreach ($computer as $row){
            $info = explode('/',$row);
            $vo['key'] = $info[0].' 版本';
            if (count($info)>1){
                $vo['value'] = $info[1];
                $computerInfo[] = $vo;
            }
        }
        $computerInfo[] = ['key'=>'系统时间','value'=>date('Y-m-d H:i:s')];
        $computerInfo[] = ['key'=>'系统时区','value'=>date_default_timezone_get()];
        $computerInfo[] = ['key'=>'操作系统','value'=>php_uname('s') . ' ' . gethostbyname($_SERVER['SERVER_NAME'])];
        return $this->fetch('',['info'=>$computerInfo]);
    }

}