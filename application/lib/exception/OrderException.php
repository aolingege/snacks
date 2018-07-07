<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/9
 * Time: 20:56
 */

namespace app\lib\exception;


class OrderException extends BaseException
{
    public $code = 404;
    public $errorCode = 80000;
    public $msg = "订单不存在，请检查ID";
}