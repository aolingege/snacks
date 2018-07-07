<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/5/1
 * Time: 17:27
 */

namespace app\index\validate;

load_trait('controller/Jump');
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{

    use \traits\controller\Jump;

    public function goCheck()
    {
        //得到参数
        $request = Request::instance();
        $params = $request->param();
        //调用子类的验证
        $result = $this->check($params);
        if (!$result){
            //通用错误
            $this->error(is_array($this->error)?implode(';', $this->error) : $this->error);
        }else{
            return true;
        }
    }


}