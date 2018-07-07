<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/4/11
 * Time: 14:24
 */
namespace app\lib\enum;

class OrderStatusEnum{

    //待支付
    const UNPAID = 1;

    //已支付
    const PAID = 2;

    //已发货
    const DELIVERED = 3;

    //已支付，但库存不足
    const PAID_BUT_OUT_OF = 4;
}