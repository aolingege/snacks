<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/4/22
 * Time: 11:43
 */

namespace app\index\controller;
use think\Request;
use think\Session;
use app\common\model\Cart as CartModel;

class Cart extends Base
{
    /**展示购物车
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if (!$this->isLogin){
            $pre = Session::get('currentPage','page');
            $this->error('请登录后查看购物车',$pre);
        }
        $CartMode = new CartModel();
        $cart = $CartMode->getCart();
        return $this->fetch('',['cart'=>$cart,'title'=>'购物车']);
    }

    /**跳转到购物车
     * @return \think\response\Json
     */
    public function SaveCartToSession()
    {
        $id = Request::instance()->post('id');
        $count = Request::instance()->post('count');
        if (!intval($id) || $id <= 0 || $count<=0 || !intval($count))
            return json(['info'=>'参数错误','status'=>false]);
        Session::delete('cartTemp','cart');
        Session::set('cartTemp',['id'=>$id,'count'=>$count],'cart');
        return json(['status'=>true,'url'=>url('cart/Index'),'info'=>'添加成功,跳转中'],200);
    }


    /**更新购物车信息
     *
     */
    public function SaveCart()
    {
        $cart = Request::instance()->post('cart');
        if (empty($cart))
            return json(['status'=>0],403);
        $CartMode = new CartModel();
        $rs = $CartMode->saveCart($cart);
        if ($rs)
            return json(['status'=>'ok']);
        else
            return json(['status'=>0],403);
    }


}

