<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/4/11
 * Time: 20:06
 */

namespace app\api\validate;


class PagingParameter extends BaseValidate
{
    protected $rule = [
      'page'=>'isPosttiveInteger',
      'size'=>'isPosttiveInteger'
    ];

    protected $message = [
       'page'=>'分页参数必须是正整数',
       'size'=>'分页参数必须是正整数'
    ];

}