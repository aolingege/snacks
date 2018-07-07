<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/6
 * Time: 20:09
 */

namespace app\lib\exception;


class ThemeException extends BaseException
{
        public $code = 400;
        public $msg = '请求的主题不存在';
        public $errorCode = 50004;
}