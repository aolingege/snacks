<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/9
 * Time: 19:58
 */

namespace app\api\service;


use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\api\model\Order as OrderModel;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Db;
use think\Exception;

class Order
{
    protected $oProducts;

    protected $products;

    protected $uid;


    /**创建订单
     * 用户ID
     * @param $uid
     * 用户上传的订单详情
     * @param $oProducts
     * @return array
     * @throws Exception
     * @throws OrderException
     * @throws UserException
     * @throws \Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function place($uid,$oProducts)
    {
        //对比数据库和客户端
        //用户
        $this->oProducts = $oProducts;
        //DB
        $this->products = $this->getProductsByOrder($oProducts);
        $this->uid =$uid;
        $status = $this->getOrderStatus();
        if (!$status['pass']){
            //失败
            $status['order_id'] = -1;
            return $status;
        }
        //订单快照
        $orderSnap = $this->snapOrder($status);
        //创建订单
        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;
        return $order;
    }

    /**创建订单
     * 订单快照
     * @param $snap
     * @return array
     * @throws Exception
     * @throws \Exception
     */
    private function createOrder($snap){
        Db::startTrans();
        try{
            $orderNo = self::makeOrderNo();
            $order = new \app\api\model\Order();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);
            $order->save();
            //保存的订单信息
            $orderID = $order->id;
            $create_time = $order->create_time;
            foreach ($this->oProducts as &$p){
                $p['order_id'] = $orderID;
            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);
            //提交事务
            Db::commit();
            return [
                'order_no'=> $orderNo,
                'order_id'=>$orderID,
                'create_time'=>$create_time
            ];
        }catch (Exception $ex){
            //回滚
            Db::rollback();
            throw  $ex;
        }
    }

    /**生成订单信息
     * @return string
     */
    public static function makeOrderNo()
    {
        $yCode = array('A','B','C','D','E','F','G','H','I','J');
        $orderSn =
            $yCode[intval(date('Y')) - 2018].strtoupper(dechex(date('m'))).date('d')
            .substr(time(),-5).substr(microtime(),2,5).sprintf('%02d',rand(0,99));
        return $orderSn;
    }

    /**检查库存
     * 订单ID
     * @param $orderID
     * @return array
     * @throws OrderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkOrderStock($orderID){
        //得到订单里的产品信息
        $oProducts = OrderProduct::where('order_id','=',$orderID)
            ->select();
        $this->oProducts = $oProducts;
        //得到产品详细信息
        $this->products  = $this->getProductsByOrder($oProducts);
        //检查库存
        $status = $this->getOrderStatus();
        return $status;
    }



    /**生成订单快照
     * @param $status
     * @return array
     * @throws Exception
     * @throws UserException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function snapOrder($status)
    {
        $snap = [
            'orderPrice' =>0,
            'totalCount' =>0,
            'pStatus'=>[],
            'snapAddress' =>null,
            'snapName'=>'',
            'snapImg'=>''
        ];
        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];
        $snap['snapAddress'] = json_encode($this->getUserAddress());
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];
        if (count($this->products)>1){
            $snap['snapName'] .= '等';
        }
        return $snap;
    }

    /**通过uid查找用户地址
     * @return array
     * @throws Exception
     * @throws UserException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getUserAddress()
    {
        $userAddress = UserAddress::where('user_id','=',$this->uid)->find();
        if (!$userAddress){
            throw new UserException([
                 'msg'=>'用户收货地址不存在，下单失败',
                 'errorCode'=>60001,
            ]);
        }
        return $userAddress->toArray();
    }

    /**检查小程序上传的数据是否正确，是否有库存
     * @return array
     * @throws OrderException
     */
    private function getOrderStatus(){
        $status = [
            'pass'=>true,
            'orderPrice'=>0,
            'totalCount'=>0,
            'pStatusArray'=>[]
        ];
        foreach ($this->oProducts as $oProduct){
            //如果haveStock为true，订单为正常，不然缺货
            $pStatus = $this->getProductStatus(
                $oProduct['product_id'],$oProduct['count'],$this->products
            );
            if (!$pStatus['haveStock']){
                $status['pass'] = false;
            }
            $status['orderPrice'] += $pStatus['totalPrice'];
            $status['totalCount'] += $pStatus['counts'];
            array_push($status['pStatusArray'],$pStatus);
        }
        return $status;
    }

    /**检查商品状态
     * 用户上传的商品ID
     * @param $oPID
     * 用户上传的商品数量
     * @param $oCount
     * 数据库中商品的信息
     * @param $products
     * @return array
     * @throws OrderException
     */
    private function getProductStatus($oPID,$oCount,$products)
    {
        $pIndex = -1;

        $pStatus = [
          'id'=>null,
          'haveStock'=>false,
          'counts'=>0,
          'price'=>0,
          'name'=>'',
          'totalPrice'=> 0,
          'main_img_url' => null
        ];
        for($i=0;$i<count($products);$i++){
           if ($oPID == $products[$i]['id']){
               $pIndex = $i;
               break;
           }
        }
        if ($pIndex == -1){
            throw new OrderException([
                'msg'=>'id为'.$oPID.'商品不存在，创建订单失败'
            ]);
        }else{
            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['counts'] = $oCount;
            $pStatus['price'] = $product['price'];
            $pStatus['name'] = $product['name'];
            $pStatus['main_img_url'] = $product['main_img_url'];
            $pStatus['totalPrice'] = $product['price'] * $oCount;
            if ($product['stock'] - $oCount >= 0){
                $pStatus['haveStock'] = true;
            }
        }
        return $pStatus;
    }

    /**通过产品ID获得产品信息
     * @param $oProducts
     * @return mixed
     * @throws \think\exception\DbException
     */
    private function getProductsByOrder($oProducts)
    {
        $oPIDs = [];
        foreach ($oProducts as $item){
            array_push($oPIDs,$item['product_id']);
        }
        //获取用户订单的全部信息
        $Products = Product::all($oPIDs)
            ->visible(['id','price','stock','name','main_img_url'])->toArray();
        return $Products;
    }


    /**
     * @param $orderID
     * @param string $jumpPage
     * @return bool
     * @throws OrderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delivery($orderID, $jumpPage = '')
    {
        $order = OrderModel::where('id', '=', $orderID)
            ->find();
        if (!$order) {
            throw new OrderException();
        }
        if ($order->status != OrderStatusEnum::PAID) {
            throw new OrderException([
                'msg' => '未付款或已发货',
                'errorCode' => 80002,
                'code' => 403
            ]);
        }
        $order->status = OrderStatusEnum::DELIVERED;
        $order->save();
        $message = new DeliveryMessage();
        return $message->sendDeliveryMessage($order, $jumpPage);
    }


}