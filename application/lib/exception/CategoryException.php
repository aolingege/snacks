<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/6
 * Time: 22:51
 */

namespace app\lib\exception;


class CategoryException extends BaseException
{
    public $code = 404;
    public $errorCode = 50003;
    public $msg = "暂无分类信息";

}