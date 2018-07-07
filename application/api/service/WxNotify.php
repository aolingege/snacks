<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/4/11
 * Time: 16:40
 */

namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\api\model\Product as ProductModel;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Loader;
use think\Exception;
use think\Log;

Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');
class WxNotify extends \WxPayNotify
{
    /**重写微信方法
     * @param array $data
     * @param string $msg
     * @return bool|
     */
    public function NotifyProcess($data,&$msg)
    {
        if ($data['result_code'] == 'SUCCESS'){
            $orderNo = $data['out_trade_no'];
            Db::startTrans();
            try{
                $order = OrderModel::where('order_no','=',$orderNo)
                    ->lock(true)->find();
                //订单状态正常，未支付
                if ($order->status == 1){
                    $service = new OrderService();
                    //检查库存
                    $stockStatus = $service->checkOrderStock($order->id);
                    if ($stockStatus['pass']){
                        $this->updateOrderStatus($order->id,true);
                        $this->reduceStock($stockStatus);
                    }else{
                        $this->updateOrderStatus($order->id,false);
                    }
                }
                Db::commit();
                return true;
            }catch (Exception $ex){
                Log::error($ex);
                Db::rollback();
                return false;
            }
        }else{
            return true;
        }
    }

    /**减少库存
     * @param $stockStatus
     * @throws Exception
     */
    private function reduceStock($stockStatus){
        foreach ($stockStatus['pStatusArray'] as $singlePStatus){
            ProductModel::where('id','=',$singlePStatus['id'])
                    ->setDec('stock',$singlePStatus['counts']);
        }
    }

    /**更新状态
     * @param $orderID
     * @param $success
     */
    private function updateOrderStatus($orderID,$success)
    {
        $status = $success ? OrderStatusEnum::PAID:OrderStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where('id','=',$orderID)->update(['status'=>$status]);
    }
}