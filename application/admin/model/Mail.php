<?php
/**
 * Created by PhpStorm.
 * User: fangaolin
 * Date: 2018/4/26
 * Time: 15:47
 */
namespace app\admin\model;

use think\Db;
use think\Model;
use app\admin\model\Auth;

class Mail extends Model
{


    /**会员通过
     * @param $id
     * @return bool|int|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function pass($id)
    {
        if ($id){
            $rs = Db::table('bis')->update(['id'=>$id,'status'=>1]);
            if ($rs){
                $rs = Db::table('bis')->where(['id'=>$id])->find();
                $content = "<h2>恭喜您，您的审核信息已经通过</h2><br/>";
                $content .= "<h3>后台管理账号:{$rs['account']}</h3>";
                $send =  \phpmailer\Email::send($rs['email'],'审核通过通知',$content);
                if ($send){
                    $account['password'] = $rs['password'];
                    $account['user_name'] = $rs['account'];
                    $account['title'] = $rs['name'];
                    $account['individuation'] = 'green';
                    return Db::table('admin_member')->insertGetId($account);
                }else{
                    return false;
                }
            }
        }
    }


    /**停用商家账号
     * @param $id
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function stop($id)
    {
        if ($id){
            $rs = Db::table('bis')->update(['id'=>$id,'status'=>0]);
            if ($rs){
                $rs = Db::table('bis')->where(['id'=>$id])->find();
                $content = "<h2>抱歉，您的账号信息被暂时停用</h2><br/>";
                $content .= "<h3>停用商户账号:{$rs['account']}</h3>";
                $send =  \phpmailer\Email::send($rs['email'],'账号停用通知',$content);
                if ($send){
                    $account['password'] = $rs['password'];
                    $account['user_name'] = $rs['account'];
                    $account['title'] = $rs['name'];
                    $dbAccount = Db::table('admin_member')->where($account)->find();
                    if ($dbAccount)
                        return Auth::deleteUser($dbAccount['id']);
                    else
                        return false;
                }else{
                    return false;
                }
            }
        }
    }

}