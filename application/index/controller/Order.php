<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/4/22
 * Time: 11:43
 */

namespace app\index\controller;

use app\api\service\WxNotify;
use app\common\model\Cart as CartModel;
use app\common\model\Order as OrderModel;
use app\api\model\Order as OrderApiModel;
use app\common\model\Product as ProductModel;
use app\index\controller\User as UserModel;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use think\Config;
use think\Db;
use think\Loader;
use think\Request;
use think\Session;

//加载微信支付SDK
// extend/WxPay/WxPay.Api.php
Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');
// 加载支付宝SDK

Loader::import('alipay.pagepay.buildermodel.AlipayTradePagePayContentBuilder',EXTEND_PATH,'.php');
Loader::import('alipay.pagepay.service.AlipayTradeService',EXTEND_PATH,'.php');
class Order extends Base
{

    /**确认订单页面
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if (!$this->isLogin){
            $this->error('请登录！',url('user/Login'));
        }
        $userInfo = Session::get('user','Index');
        $uid = $userInfo['id'];
        //读取用户地址信息
        $UserModel = new UserModel();
        $address =$UserModel->getUserAddress($uid);
        if (!$address){
            $address = 0;
        }
        $orderInfo = Session::get('orderTemp');
        $info = [];
        $ProductModel =  new ProductModel();
        foreach ($orderInfo as $row){
            $rs = $ProductModel->getProduct($row[1]);
            if ($rs){
                if ($rs['stock'] > $row[0]){
                    $rs['count'] = $row[0];
                }else{
                    $rs['name'] = $rs['name'].'(库存仅'.$rs['stock'].')';
                    $rs['count'] = $rs['stock'];
                }
                $info[] = $rs;
            }else{
                $row['name'] = '商品已下架了';
                $row['price'] = 0;
                $row['stock'] = 0;
                $info[] = $row;
            }
        }
        return $this->fetch('',['title'=>'确认订单','address'=>$address,'order'=>$info]);
    }


    /**支付页面
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function pay()
    {
        if (!$this->isLogin){
            $this->error('请登录！',url('user/Login'));
        }
        $id = Request::instance()->get('id');
        if ($id){
            $order = ['order_id'=>$id];
            Session::set('order',$order);
        }else{
            $order = Session::get('order');
        }
        $orderModel = new OrderModel();
        $orderInfo = $orderModel->getOrder($order);
        Session::delete('orderTemp');
        return $this->fetch('',['order'=>$orderInfo,'title'=>'支付页']);
    }


    /**查看订单
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function order()
    {
        if (!$this->isLogin){
            $this->error('请登录！',url('user/Login'));
        }
        $userInfo = Session::get('user','Index');
        $selectAll = Request::instance()->get('selectAll');
        if ($selectAll == 1){
            $order = OrderModel::where(['user_id'=>$userInfo['id']])->order('create_time desc')->select();
        }else{
            $selectAll = 0;
            $order = OrderModel::where(['user_id'=>$userInfo['id']])->limit(5)->order('create_time desc')->select();
        }
        foreach ($order as $row){
            $row->snap_items = json_decode($row->snap_items,true);
        }
        $orderCount = count($order);
        return $this->fetch('',['title'=>'订单查询','order'=>$order,'all'=>$selectAll,'orderCount'=>$orderCount]);
    }


    /**订单详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function details()
    {
        if (!$this->isLogin){
            $this->error('请登录！',url('user/Login'));
        }
        $userInfo = Session::get('user','Index');
        $orderID = Request::instance()->get('id');
        if ($orderID){
            $orderModel = new OrderModel();
            $order = $orderModel->getOrder(['order_id'=>$orderID]);
            if (!$order || ($order->user_id != $userInfo['id'])){
                $this->error('未找到你的订单',url('order/order'));
            }
        }
        return $this->fetch('',['title'=>'订单详情','order'=>$order]);
    }


    /**临时保存到session中(未确认)
     * @return \think\response\Json
     */
    public function saveOrder()
    {
        if (Request::instance()->isAjax()){
            //检查并生成订单
            $json = Request::instance()->post('json');
            $OrderArr = json_decode($json,true);
            Session::set('orderTemp',$OrderArr);
            return json(['status'=>'ok','url'=>url('Order/Index'),'info'=>'正在跳转...']);
        }
    }


    /**保存已生成的订单(确认订单)
     * @return \think\response\Json
     */
    public function saveOrderToPay()
    {
        if (Request::instance()->isAjax()){
            //检查并生成订单
            $json = Request::instance()->post('json');
            if (is_null($json)){
                return json(['status'=>0,'info'=>'提交的订单为空'],404);
            }
            $OrderArr = json_decode($json,true);
            Session::set('order',$OrderArr);
            return json(['status'=>'ok','url'=>url('order/pay'),'info'=>'正在跳转...']);
        }
    }


    /**异步保存用户地址信息
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function saveAddress()
    {
        if (Request::instance()->isAjax()){
            $addressInfo = Request::instance()->post();
            $user = Session::get('user','Index');
            $addressInfo['user_id'] = $user['id'];
            $order = new OrderModel();
            if ($order->saveAddress($addressInfo))
                return json(['status'=>'ok']);
        }
    }


    /**下订单
     * @return \think\response\Json
     * @throws \Exception
     * @throws \app\lib\exception\OrderException
     * @throws \app\lib\exception\UserException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function place()
    {
        if (Request::instance()->isAjax()){
            $json = Request::instance()->post('json');
            if (!$json){
                return json(['status'=>0,'msg'=>'上传数据异常']);
            }
            $orderInfo = json_decode($json,true);
            $products = [];
            $user = Session::get('user','Index');
            foreach ($orderInfo as $row){
                $uid = $row[2];
                if ($uid != $user['id'])
                    return json(['status'=>0,'msg'=>'上传数据异常']);
                $vo = [];
                $vo['product_id'] = $row[1];
                $vo['count'] = $row[0];
                $products[] = $vo;
            }
            $OrderService = new OrderService();
            $rs = $OrderService->place($uid,$products);
            if ($rs['pass']){
                $rs['status'] = 1;
                //清除购物车的数据
                $cartModel = new CartModel();
                $cartModel->delCart($uid,$products);
                return json($rs);
            }else{
                return json(['status'=>0,'msg'=>'库存不够']);
            }
        }
    }

    /**微信支付订单
     *
     */
    public function weichatPayVirtual()
    {
        if (Request::instance()->isAjax()){
            //检查并生成订单
            if (!$this->isLogin){
                return json(['status'=>0,'msg'=>'未登录'],406);
            }
            $order = Session::get('order');
            $orderModel = new OrderModel();
            $orderInfo = $orderModel->getOrder($order);
            if ($orderInfo->status != OrderStatusEnum::UNPAID){
                return json(['status'=>0,'msg'=>'订单已支付']);
            }
            $orderService = new OrderService();
            //库存量检测
            $status = $orderService->checkOrderStock($orderInfo->id);
            if ($status['pass']){
                //库存通过模拟收到微信回调
                $order = OrderApiModel::get($orderInfo->id);
                $data['result_code'] = 'SUCCESS';
                $data['out_trade_no'] =  $order->order_no;
                $notify = new WxNotify();
                $msg = '模拟支付';
                $rs = $notify->NotifyProcess($data,$msg);
                if (!$rs)
                    $status['pass'] = false;
            }
            return $status;
        }
    }



    public function alipay()
    {
        //检查并生成订单
        if (!$this->isLogin){
            $this->error('账号验证失败');
        }
        $order = Session::get('order');
        $orderModel = new OrderModel();
        $orderInfo = $orderModel->getOrder($order);
        if ($orderInfo->status != OrderStatusEnum::UNPAID){
            $this->error('订单已支付');
        }
        $orderService = new OrderService();
        //库存量检测
        $status = $orderService->checkOrderStock($orderInfo->id);
        if ($status['pass']){
            //获得订单信息
            $order = OrderApiModel::get($orderInfo->id);
            $out_trade_no = $order->order_no;
            //订单名称，必填
            $subject = $order->snap_name;
            //付款金额，必填
            $total_amount = $order->total_price;
            //商品描述，可空
            $body = '零食商城';
            //超时时间
            //构造参数
            $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
            $payRequestBuilder->setBody($body);
            $payRequestBuilder->setSubject($subject);
            $payRequestBuilder->setTotalAmount($total_amount);
            $payRequestBuilder->setOutTradeNo($out_trade_no);
            $aop = new \AlipayTradeService(Config::get('alipay'));
            /**
             * pagePay 电脑网站支付请求
             * @param $builder 业务参数，使用buildmodel中的对象生成。
             * @param $return_url 同步跳转地址，公网可以访问
             * @param $notify_url 异步通知地址，公网可以访问
             * @return $response 支付宝返回的信息
             */
            $response = $aop->pagePay($payRequestBuilder,Config::get('alipay.return_url'),Config::get('alipay.notify_url'));
            var_dump($response);
        }
    }

    /**支付宝支付成功页面
     * @throws \Exception
     */
    public function paySuccess()
    {
        $arr=$_GET;
        $alipaySevice = new \AlipayTradeService(Config::get('alipay'));
        $result = $alipaySevice->check($arr);
        if($result) {//验证成功
            //商品订单
            $out_trade_no = htmlspecialchars($_GET['out_trade_no']);
            //支付宝交易订单号
            $trade_no = htmlspecialchars($_GET['trade_no']);
            $tips = '支付成功！';
        }
        else {
            $tips = '支付失败！';
        }
        return $this->fetch('',['tip'=>$tips]);
    }

    /**支付宝回调
     * @throws \Exception
     */
    public function alipayNotify()
    {
        $arr=$_POST;
        $alipaySevice = new \AlipayTradeService(Config::get('alipay'));
        $alipaySevice->writeLog(var_export($_POST,true));
        $result = $alipaySevice->check($arr);
        if($result) {//验证成功
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];
            //交易状态
            $trade_status = $_POST['trade_status'];
            file_put_contents('post.txt',$_POST);
            $orderInfo = Db::table('order')->where(['order_no'=>$out_trade_no])->find();
            Db::table('order')->update(['id'=>$orderInfo['id'],'prepay_id'=>$trade_no]);
            if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                $data['result_code'] = 'SUCCESS';
                $data['out_trade_no'] = $out_trade_no;
                $notify = new WxNotify();
                $msg = '模拟支付';
                $rs = $notify->NotifyProcess($data,$msg);
                if (!$rs)
                    $status['pass'] = false;
            }
            echo "success";		//请不要修改或删除
        }else {
            echo "fail";	//请不要修改或删除
        }
    }


}