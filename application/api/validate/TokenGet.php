<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/7
 * Time: 14:30
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule = [
      'code'=>'require|isNotEmpty'
    ];

    protected $message = [
        'msg'=>'没有code无法获取Token'
    ];
}