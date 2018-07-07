<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/5
 * Time: 15:29
 */

namespace app\lib\exception;

class BannerMissException extends BaseException
{
    public function __construct()
    {

        parent::__construct(['code'=>404,'msg'=>'请求的Banner不存在','errorCode'=>40004]);
    }
}