<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/7
 * Time: 14:29
 */

namespace app\api\controller\v1;


use app\api\model\User;
use app\api\service\AppToken;
use app\api\service\UserToken;
use app\api\validate\AppTokenGet;
use app\api\validate\TokenGet;
use app\lib\exception\ParameterException;
use app\api\service\Token as TokenService;
use think\Request;

class Token
{
    /**得到用户令牌
     * 微信登录code凭证
     * @param string $code
     * @return array
     * @throws ParameterException
     * @throws \think\Exception
     */
    public function getToken($code = '')
    {
        (new TokenGet())->goCheck();
        $ut = new UserToken($code);
        $token = $ut->get();
        return [
            'token'=>$token
        ];
    }

    /**验证令牌是否存在
     * @param string $token
     * @return array
     * @throws ParameterException
     */
    public function verifyToken($token='')
    {
        if(!$token){
            throw new ParameterException([
                'token不允许为空'
            ]);
        }
        $valid = TokenService::verifyToken($token);
        return [
          'isValid' => $valid
        ];
    }


    /**
     * 第三方应用获取令牌
     * @url /app_token?
     * @POST ac=:ac se=:secret
     */
    public function getAppToken($ac='', $se='')
    {
        (new AppTokenGet())->goCheck();
        $app = new AppToken();
        $token = $app->get($ac, $se);
        return [
            'token' => $token
        ];
    }


    /**保存用户信息
     * @throws ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function saveUserInfo(){
        $data['nickname'] = Request::instance()->post('nickname');
        $data['extend'] = Request::instance()->post('extend');
        if(!$data){
            throw new ParameterException([
                '提交信息不能为空'
            ]);
        }
        $uid = TokenService::getCurrentUid();
        $user = new User();
        $user->where('id',$uid)->update($data);
    }

}