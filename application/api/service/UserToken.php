<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/7
 * Time: 14:45
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;
use app\api\model\User as UserModel;

class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    /**构造器
     * UserToken constructor.
     * @param $code
     */
    public function __construct($code)
    {
        //code码为小程序给的
        $this->code = $code;
        $this->wxAppID = config('weichat.app_id');
        $this->wxAppSecret = config('weichat.app_secret');
        $this->wxLoginUrl = sprintf(config('weichat.login_url'),
            $this->wxAppID,$this->wxAppSecret,$this->code);
    }


    /**将用户code换成 openid 和 session_key
     * @return string
     * @throws Exception
     */
    public function get()
    {
         //code 换成 openid 和 session_key
         $result = curl_get($this->wxLoginUrl);
         $wxResult = json_decode($result,true);
         if(empty($wxResult)){
             throw new Exception('获取session_key及openID时异常，微信内部错误');
         }else{
             $loginFail = array_key_exists('errcode',$wxResult);
             if ($loginFail){
                 throw new Exception('获取session_key及openID时异常，微信内部错误');
             }else{
                 return $this->grantToken($wxResult);
              }
         }
    }

    /**
     * @param $wxResult
     * @return string
     * @throws TokenException
     */
    private function grantToken($wxResult){
        $openid = $wxResult['openid'];
        //根据openID查询是否已是注册用户
        $user = UserModel::getByOpenID($openid);
        if ($user){
            //已注册过
            $uid = $user->id;
        }else{
            $uid = $this->newUser($wxResult['openid']);
        }
        $cachedValue = $this->prepareCachedValue($wxResult,$uid);
        $token = $this->saveToCache($cachedValue);
        return $token;
    }

    /**保存到缓存
     * 缓存值
     * @param $cachedValue
     * @return string
     * @throws TokenException
     */
    private function  saveToCache($cachedValue)
    {
            $key = self::generateToken();
            $value = json_encode($cachedValue);
            $expire_in = config('setting.token_expire_in');
            $request = cache($key,$value,$expire_in);
            if ($request)
                return $key;
            else{
                throw new TokenException([
                    'msg' => '服务器缓存异常',
                    'errorCode' => 10005
                ]);
            }
    }

    /**准备缓存数据
     * @param $wxResult
     * @param $uid
     * @return mixed
     */
    private function prepareCachedValue($wxResult,$uid){
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        //数字越大权限越大
        $cachedValue['scope'] = ScopeEnum::User;
        return $cachedValue;
    }

    /**创建新的用户
     * @param $openid
     * @return mixed
     */
    private function newUser($openid)
    {
        $user = UserModel::create([
            'openid' => $openid
        ]);
        return $user->id;
    }

    private function processLoginError($wxResult){
            throw new WeChatException([
                'msg' => $wxResult['errmsg'],
                'errorCode' => $wxResult['errcode']
            ]);
    }
}