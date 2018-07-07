<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/4/11
 * Time: 13:20
 */

namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use think\Loader;
use think\Log;

//加载微信支付SDK
// extend/WxPay/WxPay.Api.php
Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');
class Pay
{
    private $orderID;
    private $orderNO;

    function __construct($orderID)
    {
        if (!$orderID){
            throw new Exception('订单号不允许为NULL');
        }
        $this->orderID = $orderID;
    }



    /**支付检查并拉起微信支付
     * @return array
     * @throws Exception
     * @throws OrderException
     * @throws TokenException
     * @throws \WxPayException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function pay()
    {
        //用户信息检查
        $this->checkOrderValid();
        $orderService = new OrderService();
        //库存量检测
        $status = $orderService->checkOrderStock($this->orderID);
        if (!$status['pass']){
            return $status;
        }
        return $this->makeWxPreOrder($status['orderPrice']);
    }


    /**模拟支付
     * @return array|bool
     * @throws Exception
     * @throws OrderException
     * @throws TokenException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function payVirtual()
    {
        //用户信息检查
        $this->checkOrderValid();
        $orderService = new OrderService();
        //库存量检测
        $status = $orderService->checkOrderStock($this->orderID);
        if ($status['pass']){
            //库存通过模拟收到微信回调
            $order = OrderModel::get($this->orderID);
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

    /**调用微信API预支付订单
     * @param $totalPrice
     * @return array
     * @throws Exception
     * @throws TokenException
     * @throws \WxPayException
     */
    private function makeWxPreOrder($totalPrice){
        $openid = Token::getCurrentTokenVar('openid');
        if (!$openid){
            throw new TokenException();
        }
        //SDK
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNO);
        $wxOrderData->SetTrade_type('JSAPI');
        //分为单位
        $wxOrderData->SetTotal_fee($totalPrice*100);
        $wxOrderData->SetBody('零食商城');
        $wxOrderData->SetOpenid($openid);
        //回调
        $wxOrderData->SetNotify_url(config('secure.pay_back_url'));
        return $this->getPaySignature($wxOrderData);
    }

    /**处理微信信息回调
     * 预订单对象
     * @param $wxOrderData
     * @return array
     * @throws \WxPayException
     */
    private function getPaySignature($wxOrderData)
    {
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
        if ($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] != 'SUCCESS'){
            Log::record($wxOrder,'error');
            Log::record('获取预支付订单失败','error');
        }
        //prepay_id微信给的回执
        $this->recordPreOrder($wxOrder);
        //生成支付签名
        $signature = $this->sign($wxOrder);
        return $signature;
    }

    private function sign($wxOrder)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time().mt_rand(0,1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');
        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;
        unset($rawValues['appId']);
        return $rawValues;
    }

    /**将回执ID存入order
     * @param $wxOrder
     */
    private function recordPreOrder($wxOrder)
    {
        OrderModel::where('id','=',$this->orderID)
            ->update(['prepay_id'=>$wxOrder['prepay_id']]);
    }

    /**检查订单状态 更新成功变量中的订单号
     * @return bool
     * @throws Exception
     * @throws OrderException
     * @throws TokenException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function checkOrderValid()
    {
        //DB订单信息
        $order = OrderModel::where('id','=',$this->orderID)
            ->find();
        if (!$order){
            throw new OrderException();
        }
        //当前付款用户是否为订单生成用户
        if (!Token::isValidOperate($order->user_id)){
            throw new TokenException([
               'msg'=>'订单与用户不匹配',
                'errorCode'=>10003
            ]);
        }
        if ($order->status != OrderStatusEnum::UNPAID){
            throw new OrderException([
                'msg'=>'订单已支付过',
                'errorCode'=>80003,
                'code'=>400
            ]);
        }
        $this->orderNO = $order->order_no;
        return true;
    }



}