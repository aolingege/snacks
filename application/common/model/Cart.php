<?php

namespace app\common\model;

use think\Db;
use think\Model;
use think\Session;

class Cart extends Model
{

    /**保存购物车信息到数据库
     *
     */
    public function saveCarttoSQL()
    {
        $user = Session::get('user','Index');
        $cart = Session::get('cartTemp','cart');
        //读取购物车信息
        $userCart = self::where(['uid'=>$user->id])->find();
        if (!$userCart){
            $info[] = $cart;
            $data['uid'] = $user->id;
            $data['cart_arr'] = json_encode($info);
            self::save($data);
        }else{
            //用户有购物车信息
            $json = $userCart->cart_arr;
            //用户数据库的购物车信息
            $cartArr = json_decode($json,true);
            if (is_array($cartArr)){
                $info = $this->findCart($cartArr,$cart);
                $userCart->cart_arr = json_encode($info);
                self::save($userCart->toArray(),"id='".$userCart->id."'");
            }
            else{
                //数据库数据损坏
                $info[] = $cart;
                $data['uid'] = $user->id;
                $data['cart_arr'] = json_encode($info);
                self::save($data);
            }
        }
    }

    /**比较Session与数据库中的购物车信息
     * @param $cart
     * @param $cartSession
     * @return array
     */
    public function findCart($cart,$cartSession)
    {
            if (!is_array($cart))
                $cart = $cart->toArray();
            $findID = $cartSession['id'];
            $count = $cartSession['count'];
            $flag = false;
            foreach ($cart as &$row){
                if ($findID == $row['id']){
                    $row['count'] += $count;
                    $flag = true;
                    break;
                }
            }
            if (!$flag){
                $cart[] = ['id'=>$findID,'count'=>$count];
            }
            return $cart;
    }

    /**得到购物车信息
     * @return array|bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCart()
    {
        $user = Session::get('user','Index');
        if (!$user){
            return false;
        }
        $cart = self::where(['uid'=>$user->id])->find();
        if ($cart){
            $cartArr = json_decode($cart->cart_arr,true);
            $info = [];
            $ProductModel =  new Product();
            foreach ($cartArr as $row){
                $rs = $ProductModel->getProduct($row['id']);
                if ($rs){
                    $rs['count'] = $row['count'];
                    $info[] = $rs;
                }else{
                    $row['name'] = '商品已下架了';
                    $row['price'] = 0;
                    $row['stock'] = 0;
                    $info[] = $row;
                }
            }
            return $info;
        }
        return [];
    }

    /**保存购物车信息
     * @param $cartJson
     * @return bool
     */
    public function saveCart($cartJson)
    {
        $cart = json_decode($cartJson,true);
        $user = Session::get('user','Index');
        if (empty($cart)){
            self::where(['uid'=>$user['id']])->delete();
            return true;
        }
        $data = [];
        $uid = $cart[0][0];
        if ($uid != $user['id'])
            return false;
        foreach ($cart as $row){
            $vo = [];
            $vo['id'] = $row[1];
            $vo['count'] = $row[2];
            $data[] = $vo;
        }
        $info['uid'] = $uid;
        $info['cart_arr'] = json_encode($data);
        self::update($info,['uid'=>$uid]);
        return true;
    }

    /**
     *
     */
    public function delCart($uid,$del)
    {
        if (empty($del))
            return true;
        $user = Session::get('user','Index');
        if ($uid != $user['id'])
            return false;
        $cart = self::get(['uid'=>$uid]);
        if (is_null($cart)){
            return true;
        }
        $cartJson = $cart->cart_arr;
        $cartArr = json_decode($cartJson,true);
        foreach ($del as $row){
            foreach ($cartArr as $key=>&$vo){
                if ($row['product_id'] == $vo['id']){
                      $vo['count'] = $vo['count']-$row['count'];
                      if ($vo['count']<=0){
                          unset($cartArr[$key]);
                      }
                      break;
                }
            }
        }
        if (!empty($cartArr)){
            $cartJson = json_encode($cartArr);
        }else{
            self::where(['id'=>$cart->id])->delete();
            return true;
        }
        $cart = $cart->toArray();
        $cart['cart_arr'] =$cartJson;
        self::update($cart,['id'=>$cart['id']]);
        return true;
    }

}