<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/4/18
 * Time: 15:09
 */

namespace app\api\validate;


class AppTokenGet extends BaseValidate
{
    protected $rule = [
        'ac' => 'require|isNotEmpty',
        'se' => 'require|isNotEmpty'
    ];
}