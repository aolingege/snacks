<?php
/**
 * Created by PhpStorm.
 * User: fangaolin
 * Date: 2018/5/18
 * Time: 16:00
 */

namespace app\admin\controller;
use app\admin\model\Auth as AuthModel;
use app\admin\model\User as UserModel;
use think\Request;

class Control extends Admin
{


    /**添加控制器
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addControl()
    {
        $AuthModel = new AuthModel();
        $allRules = $AuthModel->getAllrule();
        return $this->fetch('',['ruleInfo'=>$allRules]);
    }


    /**更新一条数据
     * @param string $id
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function updateControl($id='')
    {
        //如果是提交优先处理
        if (Request::instance()->isPost()){
            $post = Request::instance()->post();
            if ($post['id'] == ''){
                unset($post['id']);
                $rs = AuthModel::addRule($post);
            }else{
                $rs = AuthModel::updateRule($post);
            }
            if ($rs){
                return json(['status'=>1,'info'=>'操作成功']);
            }else{
                return json(['status'=>0,'info'=>'无变动']);
            }
        }
        $AuthModel = new AuthModel();
        $rule = $AuthModel->getRuleByID($id);
        if ($rule){
            $this->assign('data',$rule);
        }
        if ($id)
            $title = '编辑';
        else
            $title = '添加';
        return $this->fetch('',['title'=>$title]);
    }


    /**部门管理
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function groupControl()
    {
        $groupInfo = AuthModel::getGroupAll();
        return $this->fetch('',['info'=>$groupInfo]);
    }


    /**更新或编辑分组信息
     * @param $id
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function updateGroup($id='')
    {
        if (Request::instance()->isPost()){
            $post = Request::instance()->post();
            if ($post['id'] == ''){
                unset($post['id']);
                $rs = AuthModel::addGroup($post);
            }else{
                $rs = AuthModel::updateGroup($post);
            }
            if ($rs){
                return json(['status'=>1,'info'=>'操作成功']);
            }else{
                return json(['status'=>0,'info'=>'无变动']);
            }
        }
        if ($id){
            $this->assign('title','编辑');
            $simple = AuthModel::getGroupSimple($id);
            if ($simple)
                $this->assign('data',$simple);
        }else{
            $this->assign('title','添加');
        }
        return $this->fetch();
    }
    
    
    /**异步删除一条信息
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteSimple()
    {
        $id = Request::instance()->post('id');
        $result = AuthModel::deleteRule($id);
        if ($result){
            return json(['status'=>1,'msg'=>'删除成功'],200);
        }else{
            return json(['status'=>0,'msg'=>'删除失败'],503);
        }
    }


    /**异步删除多条
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteMult()
    {
        if (Request::instance()->isAjax()){
            $id = $_POST['id'];
            if ($id){
                foreach ($id as $row){
                    $rs = AuthModel::deleteRule($row);
                    if (!$rs){
                        return json(['status'=>0,'info'=>'部分删除失败']);
                    }
                }
                return json(['status'=>1,'info'=>'删除成功']);
            }else
                return json(['status'=>0,'info'=>'删除失败']);
        }
    }


    /**删除一条部门
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteGroup()
    {
        $id = Request::instance()->post('id');
        $result = AuthModel::deleteGroup($id);
        if ($result){
            return json(['status'=>1,'msg'=>'删除成功'],200);
        }else{
            return json(['status'=>0,'msg'=>'删除失败'],503);
        }
    }

    
    /**删除多条部门
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteGroupMult()
    {
        if (Request::instance()->isAjax()){
            $id = $_POST['id'];
            if ($id){
                foreach ($id as $row){
                    $rs = AuthModel::deleteGroup($row);
                    if (!$rs){
                        return json(['status'=>0,'info'=>'部分删除失败']);
                    }
                }
                return json(['status'=>1,'info'=>'删除成功']);
            }else
                return json(['status'=>0,'info'=>'删除失败']);
        }
    }


    /**权限管理
     * @param string $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function authority($id='')
    {
        $rules = [];
        if ($id){
            $rules = AuthModel::getGroupRule($id);
        }
        $allRules = AuthModel::getRuleCategory();
        return $this->fetch('',['idRules'=>json_encode($rules),'id'=>$id,
            'allRules'=>$allRules]);
    }


    /**部门权限管理
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function authPromo()
    {
        $rules = Request::instance()->post();
        $rules['rules'] = join(',',$rules['rule']);
        unset($rules['rule']);
        $rs = AuthModel::updateGroup($rules);
        if ($rs){
            return json(['status'=>1,'info'=>'操作成功']);
        }else{
            return json(['status'=>0,'info'=>'无变动']);
        }
    }


    /**用户管理
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function userManage()
    {
        $allUser = UserModel::getAllUser();
        return $this->fetch('',['info'=>$allUser]);
    }


    /**删除一条用户
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteUserSimple()
    {
        $id = Request::instance()->post('id');
        $result = AuthModel::deleteUser($id);
        if ($result){
            return json(['status'=>1,'msg'=>'删除成功'],200);
        }else{
            return json(['status'=>0,'msg'=>'删除失败'],503);
        }
    }

    /**删除多条用户
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteUserMult()
    {
        if (Request::instance()->isAjax()){
            $id = $_POST['id'];
            if ($id){
                foreach ($id as $row){
                    $rs = AuthModel::deleteUser($row);
                    if (!$rs){
                        return json(['status'=>0,'info'=>'部分删除失败']);
                    }
                }
                return json(['status'=>1,'info'=>'删除成功']);
            }else
                return json(['status'=>0,'info'=>'删除失败']);
        }
    }

    /**重置用户密码
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function resetUserPass()
    {
        $id = Request::instance()->post('id');
        $result = UserModel::setPass($id);
        if ($result){
            return json(['status'=>1,'msg'=>'重置成功'],200);
        }else{
            return json(['status'=>0,'msg'=>'重置失败'],503);
        }
    }


    /**添加用户
     * @return mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addUser()
    {
        if (Request::instance()->isPost()){
            $post['password'] = Request::instance()->post('passwordOne');
            $post['user_name'] = Request::instance()->post('account');
            $post['title'] = Request::instance()->post('title');
            $post['individuation'] = 'green';
            $rs = UserModel::addUser($post);
            if ($rs){
                return json(['status'=>1,'info'=>'添加成功']);
            }else{
                return json(['status'=>0,'info'=>'账号已被注册']);
            }
        }
        return $this->fetch();
    }


    /**分配部门
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function distribution($id)
    {
        if (Request::instance()->isPost()){
            $group = Request::instance()->post();
            $rs = UserModel::updateUserGroup($group['id'],$group['group']);
            if ($rs){
                return json(['status'=>1,'info'=>'操作成功']);
            }else{
                return json(['status'=>0,'info'=>'无变动']);
            }
        }
        $group = AuthModel::getGroupAll();
        $userGroup = UserModel::getUserGroup($id);
        return $this->fetch('',['group'=>$group,'user'=>$userGroup,'id'=>$id]);
    }


}