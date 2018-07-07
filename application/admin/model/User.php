<?php
/**
 * Created by PhpStorm.
 * User: fangaolin
 * Date: 2018/4/26
 * Time: 15:47
 */
namespace app\admin\model;

use think\Db;
use think\Loader;
use think\Model;
use think\Session;
Loader::import('baiduMap.Map',EXTEND_PATH,'.php');
class User extends Model
{
    protected $table = 'admin_member';

    /**检查用户的身份信息
     * 用户名
     * @param $user
     * 密码
     * @param $psw
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkUserInfo($user,$psw)
    {
        $rs = Db::table($this->table)->where(['user_name'=>$user,'password'=>md5($psw)])->find();
        Session::set('admin',$rs,'Login');
        return $rs;
    }

    /**用户换肤
     * @param $color
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function changedSkin($color)
    {
        if (!$color)
            return false;
        $admin = Session::get('admin','Login');
        if (Db::table($this->table)->where($admin)->update(['individuation'=>$color])){
            $admin['individuation'] = $color;
            Session::set('admin',$admin,'Login');
            return true;
        }else
            return false;
    }


    /**得到所有用户与部门信息
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getAllUser()
    {
        $allUser = Db::table('admin_member')->select();
        $access = Db::table('admin_auth_group_access')->select();
        $userGroup = [];
        //得到所有用户所在部门的信息
        foreach ($access as $row){
            $userGroup[$row['uid']][] =  $row['group_id'];
        }
        $allGroup = Auth::getGroupAll();
        $groupInfo = [];
        foreach ($allGroup as $row){
            $groupInfo[$row['id']] = $row['title'];
        }
        $allUser = $allUser->toArray();
        foreach ($allUser as $key=>$row){
                if (isset($userGroup[$row['id']])){
                    $group = $userGroup[$row['id']];
                    $tempGroup = [];
                    foreach ($group as $vo){
                        $tempGroup[] = $groupInfo[$vo];
                    }
                    $allUser[$key]['group'] = join(' | ',$tempGroup);
                }else{
                    $allUser[$key]['group'] = '暂无';
                }
        }
        return $allUser;
    }


    /**得到单一用户
     * @param $id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUserGroup($id)
    {
        if ($id){
            $info =  Db::table('admin_auth_group_access')->where(['uid'=>$id])->select();
            $group = [];
            foreach ($info as $row){
                $group[] = $row['group_id'];
            }
            return join(',',$group);
        }
    }


    /**重置用户密码
     * @param $id
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function setPass($id)
    {
        if ($id){
            $map['id'] = $id;
            $map['password'] = md5('123456');
            return Db::table('admin_member')->update($map);
        }
    }

    /**添加用户
     * @param $data
     * @return bool|int|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function addUser($data)
    {
        if ($data){
            if (Db::table('admin_member')->where(['user_name'=>$data['user_name']])->find())
                return false;
            return Db::table('admin_member')->insertGetId($data);
        }
    }

    /**更新用户与部门的信息
     * @param $id
     * @param $group
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function updateUserGroup($id,$group)
    {
        if ($group && $id){
            foreach ($group as $row){
                if (Db::table('admin_auth_group_access')->where(['group_id'=>$row,'uid'=>$id])->find())
                    continue;
                Db::table('admin_auth_group_access')->insertGetId(['group_id'=>$row,'uid'=>$id]);
            }
            return true;
        }
    }


    /**保存商家申请信息
     * @param $data
     * @return int|string
     */
    public static function saveBisInfo($data)
    {
        if ($data){
            $data['password'] = md5($data['passwordOne']);
            $data['status'] = 0;
            unset($data['s_province']);
            unset($data['s_city']);
            unset($data['s_county']);
            unset($data['detail']);
            unset($data['passwordOne']);
            unset($data['passwordTwo']);
            if (!isset($data['license']))
                unset($data['license']);
            $data['create_time'] = time();
            return Db::table('bis')->insertGetId($data);
        }
    }


    /**保存商家地址信息
     * @param $data
     * @param $bisID
     * @return int|string
     */
    public static function saveBisAddress($data,$bisID)
    {
        if ($data){
            if (isset($data['country']))
                $address = $data['province'].$data['city'].$data['country'].$data['detail'];
            else
                $address = $data['province'].$data['city'].$data['detail'];
            $xyPoint = \Map::getLngLat($address);
            $data['ypoint'] = $xyPoint['result']['location']['lng'];
            $data['xpoint'] = $xyPoint['result']['location']['lat'];
            $data['bis_id'] = $bisID;
            $data['create_time'] = time();
            return Db::table('bis_address')->insertGetId($data);
        }
    }

    /**
     * 通过商户ID得到地址
     * @param $id
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getAddressByBisId($id)
    {
        if ($id){
            $address = Db::table('bis_address')->where(['bis_id'=>$id])->find();
            if ($address){
                if (isset($address['country']))
                    $address = $address['province'].$address['city'].$address['country'].$address['detail'];
                else
                    $address = $address['province'].$address['city'].$address['detail'];
                return ['img'=>\Map::getStaticimageSrc($address),'address'=>$address];
            }else
                return false;

        }
    }

    /**得到所有的商家
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getAllBis()
    {
        return Db::table('bis')->select();
    }

}