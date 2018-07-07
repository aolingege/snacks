<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/4/22
 * Time: 11:43
 */

namespace app\index\controller;
use app\common\model\User as UserModel;
use app\index\validate\User as UserValidate;
use think\Db;
use think\Session;

class User extends Base
{
    public function login()
    {
        if ($this->isLogin){
            $this->success('您已经登录了哦',url('Index/Index'));
        }
        return $this->fetch();
    }

    /**登录验证
     *
     */
    public function logincheck()
    {
        if (!request()->isPost()){
            $this->error('提交不合法');
        }
        $data = input('post.');
        $validate = new UserValidate();
        $validate->goCheck();
        $User = new UserModel();
        $rs = $User->checkUser($data['username'],md5($data['password']));
        if ($rs){
            Session::set('user',$rs,'Index');
            //判断上个页面是什么
            $prePage = Session::get('currentPage','page');
            if ($prePage)
                $this->redirect($prePage);
            else
                $this->redirect(url('Index/Index'));
        }else{
            $this->error('用户名和密码不匹配');
        }
    }

    /**注册页面
     * @return mixed
     */
    public function register()
    {
        if (request()->isPost()){
            $data = input('post.');
            if (!captcha_check($data['verifyCode'], 'code'))
                $this->error('验证码错误!',null,'',2);
            //验证
            $validate = new UserValidate();
            $validate->goCheck();
            if ($data['password'] != $data['repassword']){
                $this->error('两次输入的密码不一样');
            }
                unset($data['verifyCode'],$data['repassword']);
                $data['password'] = md5($data['password']);
                $rs = UserModel::create($data);
                if ($rs){
                    //保存ID
                    Session::set('user',$rs,'Index');
                    $prePage = Session::get('currentPage','page');
                    if ($prePage)
                        $this->success('注册成功',$prePage);
                    else
                        $this->success('注册成功',url('Index/Index'));
                }else{
                    $this->error('注册失败');
                }
        }else{
            return $this->fetch();
        }
    }

    /**读取用户地址信息
     * @param $uid
     * @return array|bool|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserAddress($uid)
    {
        //如果UID为空
        if (!$uid)
            return false;
        $userAddress = Db::table('user_address')->where(['user_id'=>$uid])->find();
        if ($userAddress)
            return $userAddress;
        else
            return false;
    }


    /**退出登录
     *
     */
    public function outLogin()
    {
        Session::delete('user','Index');
        $this->redirect(url('user/Login'));
    }

    
}