<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/6
 * Time: 15:47
 */

namespace app\api\validate;


class IDCollection extends BaseValidate
{
    //验证规则
    protected $rule = [
          'ids' => 'require|checkIDs'  
    ];

    //基类错误信息
    protected $message = [
      'ids' => 'ids参数必须是以逗号分隔的多个正整数'
    ];

    /**验证参数是否是通过逗号分割的整数
     * 待验证的参数
     * @param $value
     * 是否通过验证
     * @return bool
     */
    protected function checkIDs($value)
    {
        $values = explode(',',$value);
        if (empty($values)){
            return false;
        }
        foreach ($values as $id){
            if ($this->isPosttiveInteger($id) !== true){
                return false;
            }
        }
        return true;
    }

}