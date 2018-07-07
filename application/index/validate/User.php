<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/5/1
 * Time: 17:26
 */

namespace app\index\validate;


class User extends BaseValidate
{
    protected  $rule = [
        ['username', 'require|length:4,15', '未填用户名|用户名在4到15个字符之间'],
        ['password','require|length:4,15', '未填密码|密码在4到15个字符之间'],
    ];
}