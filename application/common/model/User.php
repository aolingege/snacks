<?php
namespace app\common\model;

use think\Model;
use think\Session;

class User extends Model
{

    public function checkUserBysession()
    {
        $userInfo = Session::get('user','Index');
        if (!$userInfo)
            return false;
        $info = self::get($userInfo['id']);
        if ($info->password == $userInfo['password'])
            return true;
        else
            return false;
    }

    public function checkUser($username,$password)
    {
        $info = self::where(['username'=>$username,'password'=>$password])->find();
        if ($info)
            return $info;
        else
            return false;
    }

}