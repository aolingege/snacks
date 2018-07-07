<?php
/**
 * Created by PhpStorm.
 * User: fangaolin
 * Date: 2018/4/24
 * Time: 11:35
 */
namespace app\admin\controller;

use think\Controller;
use think\Session;
use think\Config;
use think\Request;
use think\Auth as Auth;
use app\admin\model\Auth as AuthModel;

class Admin extends Controller
{

    protected $admin;
    protected $auth;

    //构造器
    public function __construct()
    {
        parent::__construct();
        //免登录控制器
        $safeController = Config::get('power.UNLIMITED_CONTROLLER');
        //检查是否登录了
        $request = Request::instance();
        $controller = $request->controller();
        $action = $request->action();
        $this->checkLogin() || in_array($controller, $safeController) || $this->redirect('Login/Index');
        //权限检查
        $safeAuthModule = Config::get('power.UNAUTHED_CONTROLLER');
        $auth = Auth::instance();
        $this->auth = $auth;
        //超级管理员无视权限
        $this->admin['id'] == 1 || ( isset($safeAuthModule[$controller]) &&  in_array($action, $safeAuthModule[$controller]) )
        || $auth->check($controller.'/'.$action,$this->admin['id']) || $this->error('对不起您没有权限');
        //开始初始化边框
        $this->initAside($this->admin['id']);
        $this->assign('controller',$controller);
        $this->assign('current',$controller.'/'.$action);
        $user = Session::get('admin','Login');
        $this->assign('admin',$user);
    }

    /**初始化边框
     * @param $uid
     */
    public function initAside($uid)
    {
        $auth = Auth::instance();
        //获得权限组信息
        $group = $auth->getGroups($uid);
        //分组获取失败并且不是超级管理员就报错
        if (!$group && $this->admin['id'] != 1)
            $this->error('对不起权限组获取失败');
        //用户可能在多个部门所以要循环查询
        $rules = array();
        foreach ($group as $row){
            $rule = $auth->getGroupAuthList($row['group_id']);
            $rules = array_merge($rules,$rule);
        }
        $rules = array_values($rules);
        $AuthModel = new AuthModel();
        $rules = $AuthModel->getUserSide($rules);
        $this->assign('rules',$rules);
    }

    /**检查是否登录
     * @return bool
     */
    protected function checkLogin()
    {
        $admin = Session::get('admin','Login');
        if (is_null($admin))
            return false;
        $this->admin = $admin;
        return $admin['id'] ? true : false;
    }

    /**映射到404
     *
     */
    public function mapping404()
    {
        echo '404';
    }

    /**退出登录
     * @param string $switch
     */
    public function outLogin($switch='')
    {
        Session::delete('admin','Login');
        if ($switch = 'true'){
              $this->redirect(url('admin/Login/Index'));
        }
    }

}