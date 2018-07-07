<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/7
 * Time: 20:04
 */

namespace app\api\validate;


class AddressNew extends BaseValidate
{
    protected $rule = [
      'name'=>'require|isNotEmpty',
//      'mobile'=>'require|isMobile',
      'mobile'=>'require|isNotEmpty',
      'province'=>'require|isNotEmpty',
      'city'=>'require|isNotEmpty',
      'country'=>'require|isNotEmpty',
      'detail'=>'require|isNotEmpty',
    ];
}