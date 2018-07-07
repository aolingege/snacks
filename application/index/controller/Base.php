<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/5/1
 * Time: 21:16
 */

namespace app\index\controller;

use app\common\model\Category;
use think\Controller;
use app\common\model\User as UserModel;
use app\common\model\Cart as CartModel;
use app\api\model\Banner as BannerModel;
use think\Request;
use think\Session;

class Base extends Controller
{
    protected $isLogin = false;

    public function __construct()
    {
        parent::__construct();
        $user = new UserModel();
        if (!$user->checkUserBysession()){
            $this->assign('Login',false);
        }
        else{
            $userInfo = Session::get('user','Index');
            $this->assign('login',$userInfo['username']);
            $this->assign('uid',$userInfo['id']);
            $this->isLogin = true;
        }
        $this->checkSession();
        //分类信息
        $Categroy = new Category();
        //得到分类的信息
        $info = $Categroy->getCategory(5);
        $banner = BannerModel::getBannerByID(2);
        $banner = $banner->toArray();
        $this->assign('controller',strtolower(request()->controller()));
        $this->assign('title','首页');
        $this->assign('category',$info);
        $this->assign('banner',$banner['items']);
    }

    public function checkSession()
    {
        $cart = Session::get('cartTemp','cart');
        //如果Session有信息
        if ($cart){
            if ($this->isLogin){
                //保存Session到数据库
                $cartModel = new CartModel();
                $cartModel->saveCarttoSQL();
            }
            //删除购物车信息
            Session::delete('cartTemp','cart');
        }
    }


    /**保存页面记录
     *
     */
    protected function savePageRecord()
    {
        $previousPage = Session::get('currentPage','page');
        if ($previousPage){
            Session::set('prePage',$previousPage,'page');
        }
        $currentPage =  Request::instance()->server('REDIRECT_URL');
        Session::set('currentPage',$currentPage,'page');
    }


}