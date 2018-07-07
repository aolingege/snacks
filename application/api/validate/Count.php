<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/6
 * Time: 21:40
 */

namespace app\api\validate;


class Count extends BaseValidate
{
    protected $rule = [
      'count'=>'isPosttiveInteger|between:1,20'
    ];
}