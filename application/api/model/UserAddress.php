<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/7
 * Time: 21:19
 */

namespace app\api\model;


class UserAddress extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $hidden = ['id','delete_time','user_id'];
}