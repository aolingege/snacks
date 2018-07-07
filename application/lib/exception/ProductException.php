<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/6
 * Time: 22:00
 */

namespace app\lib\exception;


class ProductException extends BaseException
{
    public $code = 404;
    public $errorCode = 50004;
    public $msg = "产品数据缺失";
}