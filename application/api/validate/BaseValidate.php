<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/5
 * Time: 14:21
 */

namespace app\api\validate;


use app\lib\exception\ParameterException;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
    public function goCheck()
    {
        //得到参数
        $request = Request::instance();
        $params = $request->param();
        //调用子类的验证
        $result = $this->check($params);
        if (!$result){
            //通用错误
            $e = new ParameterException([
                //传入Validate成员变量
                'msg' => is_array($this->error)?implode(';', $this->error) : $this->error
            ]);
            throw $e;
        }else{
            return true;
        }
    }


    protected function isPosttiveInteger($value,$rule = '',$data = '',$field = ''){
        //字符串转数字
        if (is_numeric($value) && is_int($value + 0 ) && ($value + 0 ) > 0 ){
            return true;
        }else{
            return false;
        }
    }

    protected function isNotEmpty($value,$rule = '',$data = '',$field = ''){
        if (empty($value)){
            return false;
        }else{
            return true;
        }
    }

    protected function isMobile($value){
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule,$value);
        if ($result){
            return true;
        }else{
            return false;
        }
    }

    /**过滤器，从用户传来的数据删除不需要的部分
     * @param $arrays
     * @return array
     * @throws ParameterException
     */
    public function getDataByRule($arrays)
    {
        if (array_key_exists('user_id',$arrays) | array_key_exists('uid',$arrays)){
            throw new ParameterException(
                [
                    'msg'=>'参数中包含有非法的参数名user_id或者uid'
                ]
            );
        }
        //从post中得到用户的请求，拿走我们需要的数据
        $newArray = [];
        foreach ($this->rule as $key=>$value){
            $newArray[$key] = $arrays[$key];
        }
        return $newArray;
    }

}