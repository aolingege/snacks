<?php
/**
 * Created by PhpStorm.
 * User: fangaolin
 * Date: 2018/4/24
 * Time: 15:04
 */
namespace app\admin\controller;

use app\admin\model\User;
use think\Controller;
use think\Request;

class Login extends Controller
{
    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        //如果是post请求
        if ($this->request->isPost()){
            $captcha = input('post.validate');
            if (!captcha_check($captcha, 'code'))
                $this->error('验证码错误!');
            $user = new User();
            if ($userLogin = $user->checkUserInfo(input('post.username'),input('post.password'))){
                $this->redirect(url('Index/Index'));
            }else{
                $this->error('密码错误!');
            }
        }
        return $this->fetch();
    }


    /**异步修改用户的皮肤
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function ajaxChangeSkin()
    {
        if($this->request->isAjax()){
            $user = new User();
            $color = input('post.color');
            $rs = $user->changedSkin($color);
            if ($rs){
                return ['status'=>1,'info'=>'换肤成功'];
            }else{
                return ['info'=>'换肤失败'];
            }
        }
    }


    /**商家申请
     * @return mixed
     */
    public function register()
    {
        return $this->fetch();
    }


    /**保存商家申请的信息
     *
     */
    public function registerSaveInfo()
    {
        if (Request::instance()->isPost()){
            $post = Request::instance()->post();
            if ($post)
                $bid = User::saveBisInfo($post);
            if (isset($bid)){
                $bisAddress['province'] = $post['s_province'];
                $bisAddress['city'] = $post['s_city'];
                if ($post['s_county'] != '市、县级市')
                    $bisAddress['country'] = $post['s_county'];
                $bisAddress['detail'] = $post['detail'];
                $rs = User::saveBisAddress($bisAddress,$bid);
                if ($rs)
                    $this->success('恭喜你,申请成功,我们会通过邮箱发送申请结果!',url('Login/Index'));
            }else
                $this->error('注册失败');
        }
    }


    /**异步上传执照
     * @return \think\response\Json
     */
    public function uploadImage()
    {
        $files = Request::instance()->file('file');
        $info = $files->move('upload');
        if ($info && $info->getPathname()){
            return json(['status'=>1,'path'=>'/'.str_replace("\\","/",$info->getPathname())]);
        }
        return json(['status'=>0]);
    }

}