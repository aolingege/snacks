<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/4/11
 * Time: 13:06
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePostiveInt;
use app\api\service\Pay as PayService;

class Pay extends BaseController
{
    protected $beforeActionList = [
      'checkExclusiveScope' => ['only'=>'getPreOrder']
    ];


    /**订单号
     * 订单ID
     * @param null $id
     * @return array
     * @throws \WxPayException
     * @throws \app\lib\exception\OrderException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPreOrder($id = null)
    {
        (new IDMustBePostiveInt())->goCheck();
        $pay = new PayService($id);
        return $pay->pay();
    }

    /**模拟微信支付
     * 订单ID
     * @param null $id
     * @return array|bool
     * @throws \app\lib\exception\OrderException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPreOrderVirtual($id = null)
    {
        (new IDMustBePostiveInt())->goCheck();
        $pay = new PayService($id);
        return $pay->payVirtual();
    }

    /**微信通知
     *
     */
    public function receiveNotify()
    {
        //重写微信方法
        $notify = new WxNotify();
        $notify->Handle();
    }
}