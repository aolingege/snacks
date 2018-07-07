<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/7
 * Time: 16:50
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;

class Token
{
    /**生成令牌
     * @return string
     */
    public static function generateToken()
    {
        //生成32位字符串
        $randChars = getRandChar(32);
        //请求时间戳
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        //加杂
        $salt = config('secure.token_salt');
        return md5($randChars.$timestamp.$salt);
    }

    /**得到当前的UID
     * @return mixed
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentUid()
    {
        //token作为键
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    /**从用户请求中的令牌去访问缓存得到想要得到的键值
     * 键名
     * @param $key
     * @return mixed
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentTokenVar($key)
    {
        $token = Request::instance()->header('token');
        $vars = Cache::get($token);
        if (!$vars){
            throw new TokenException();
        }else{
            if (!is_array($vars)){
                $vars = json_decode($vars,true);
            }
            if (array_key_exists($key,$vars)){
                return $vars[$key];
            }else{
                throw new Exception('尝试获取的Token变量并不存在');
            }
        }
    }

    //用户和管理员都可以
    public static function needPrimaryScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope){
            if($scope >= ScopeEnum::User){
                return true;
            }else{
                return new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
    }

    //只有用户才能访问的接口权限
    public static function needExclusiveScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope){
            if($scope == ScopeEnum::User){
                return true;
            }else{
                return new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
    }

    /**
     * 将被检查的用户ID
     * @param $checkedUID
     * @return bool
     * @throws Exception
     * @throws TokenException
     */
    public static function isValidOperate($checkedUID)
    {
        if (!$checkedUID){
            throw new Exception('检查UID时必须传入一个被检查的UID');
        }
        $currentOperateUID = self::getCurrentUid();
        if ($currentOperateUID == $checkedUID){
            return true;
        }else
            return false;
        
    }

    public static function verifyToken($token)
    {
        $exist = Cache::get($token);
        if ($exist){
            return true;
        }else{
            return false;
        }
    }

}