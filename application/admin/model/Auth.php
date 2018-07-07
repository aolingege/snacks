<?php
/**
 * Created by PhpStorm.
 * User: fangaolin
 * Date: 2018/4/28
 * Time: 15:57
 */

namespace app\admin\model;


use think\Db;
use think\Model;

class Auth extends Model
{

    /**将部门的原始信息过滤成边框信息
     * @param $rules
     * @return array
     */
    public function getUserSide($rules)
    {
        if (empty($rules))
            return [];
        $controller = [];
        foreach ($rules as $row){
            if ($row['level'] == 1){
                $controller[$row['name']] = $row;
            }
        }
        foreach ($rules as $row){
            if ($row['level'] == 0 && $row['show_status'] == 1){
                $belongController = explode('/',$row['name']);
                $controller[$belongController[0]]['action'][] = $row;
            }
        }
        return $controller;
    }


    /**得到所有的规则
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllrule()
    {
        $rules = Db::table('admin_auth_rule')->select();
        $field = array('方法','控制器');
        $rules = $rules->toArray();
        foreach ($rules as &$row){
            $row['level'] = $field[$row['level']];
        }
        return $rules;
    }


    /**删除一条规则
     * @param $id
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function deleteRule($id){
        if ($id){
            return Db::table('admin_auth_rule')->delete($id);
        }else
            return false;
    }

    /**删除部门
     * @param $id
     * @return bool|int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function deleteGroup($id)
    {
        if ($id){
            return Db::table('admin_auth_group')->delete($id);
        }else
            return false;
    }


    /**更新一条规则
     * @param $data
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function updateRule($data)
    {
        if ($data){
            return Db::table('admin_auth_rule')->update($data);
        }
    }

    /**插入一条数据
     * @param $data
     * @return int|string
     */
    public static function addRule($data)
    {
        if ($data){
            $data['show_status'] = 1;
            $data['sort'] = 0;
            return Db::table('admin_auth_rule')->insertGetId($data);
        }
    }


    /**通过ID得到规则
     * @param $id
     * @return array|bool|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRuleByID($id)
    {
        if (!$id)
            return false;
        return Db::table('admin_auth_rule')->where(['id'=>$id])->find();
    }


    /**得到分类后的控制器-方法
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getRuleCategory()
    {
        $rules = Db::table('admin_auth_rule')->select();
        $category = [];
        foreach ($rules as $rule){
            if ($rule['level'])
                $category[$rule['name']] = ['id'=>$rule['id'],'title'=>$rule['title']];
            else{
                $action  =  explode('/',$rule['name']);
                $category[$action[0]]['action'][] = ['id'=>$rule['id'],'title'=>$rule['title']];
            }
        }
        return $category;
    }


    /**通过ID得到部门权限
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getGroupRule($id)
    {
        if ($id){
            $rules = Db::table('admin_auth_group')->where(['id'=>$id])->find();
            if ($rules['rules']){
                return explode(',',$rules['rules']);
            }else
                return [];
        }
    }
    
    
    /**得到所有部门信息
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getGroupAll()
    {
        return Db::table('admin_auth_group')->select();
    }

    /**得到对应的部门信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getGroupSimple($id)
    {
        if (!$id)
            return false;
        return Db::table('admin_auth_group')->where("id='{$id}'")->find();
    }


    /**添加分组
     * @param $data
     * @return int|string
     */
    public static function addGroup($data)
    {
        if ($data){
            return Db::table('admin_auth_group')->insertGetId($data);
        }
    }


    /**更新分组
     * @param $data
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function updateGroup($data)
    {
        if ($data){
            return Db::table('admin_auth_group')->update($data);
        }
    }


    /**公共删除方法
     * @param $id
     * @param $table
     * @return bool|int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function deleteCommon($id,$table)
    {
        if ($id){
            return Db::table($table)->delete($id);
        }else
            return false;
    }

    /**删除用户
     * @param $id
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function deleteUser($id)
    {
        if ($id){
            if (Db::table('admin_auth_group_access')->where(['uid'=>$id])->find())
                return Db::table('admin_auth_group_access')->delete(['uid'=>$id]) &&
                        Db::table('admin_member')->delete($id);
            else
                return Db::table('admin_member')->delete($id);
        }else
            return false;
    }


}