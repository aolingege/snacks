<?php
namespace app\common\model;

use think\Db;
use think\Model;

class Order extends Model
{
    protected $autoWriteTimestamp = true;

    /**保存用户订单的地址信息
     * 地址信息
     * @param $address
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function saveAddress($address)
    {
        if ($userAddress = self::table('user_address')->where(['user_id'=>$address['user_id']])->find()){
            $address['update_time'] = time();
            self::table('user_address')->where(['id'=>$userAddress['id']])->update($address);
        }else{
            $address['create_time'] = time();
            Db('user_address') -> insert($address);
        }
        return true;
    }



    /**得到订单信息
     * 订单信息
     * @param $order
     * @return array|bool|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrder($order)
    {
        if (isset($order['order_id'])){
            $order = self::where(['id'=>$order['order_id']])->find();
            if ($order){
                $order->snap_items = json_decode($order->snap_items,true);
                $order->snap_address = json_decode($order->snap_address,true);
            }
            return $order;
        }else{
            return false;
        }
    }


    /**得到所有的订单
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getAllOrder(){
        $data = Db::table('order')->order('create_time desc')->select();
        $data = $data->toArray();
        foreach ($data as &$row){
            $row = array_merge($row,json_decode($row['snap_address'],true));
        }
        return $data;
    }


    /**已发货
     * @param $id
     * @return int|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function deliver($id)
    {
        $orderInfo = Db::table('order')->where(['id'=>$id])->find();
        $goods = json_decode($orderInfo['snap_items'],true);
        foreach ($goods as $row){
            $bis = Db::table('product_bis')->where(['product_id'=>$row['id']])->find();
            if (isset($bis['product_id'])){
                $bis['money'] = isset($bis['money']) ? $bis['money'] + $row['totalPrice'] : $row['totalPrice'];
                $bis['count'] = isset($bis['count']) ? $bis['count'] + 1 : 1;
                Db::table('product_bis')->update($bis);
                $log = [];
                $log['product_id'] = $bis['product_id'];
                $log['delivery_time'] = date('Y-m-d H:i:s');
                $log['bis_id'] = $bis['bis_id'];
                $log['money'] = $row['totalPrice'];
                $log['create_time'] = time();
                Db::table('pay_log')->insertGetId($log);
            }else{
                $log = [];
                $log['product_id'] = $row['id'];
                $log['delivery_time'] = date('Y-m-d H:i:s');
                $log['bis_id'] = 0;
                $log['money'] = $row['totalPrice'];
                $log['create_time'] = time();
                Db::table('pay_log')->insertGetId($log);
            }
        }
        $orderInfo['status'] = 3;
        return Db::table('order')->update($orderInfo);
    }

}