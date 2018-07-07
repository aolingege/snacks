<?php
/**
 * Created by PhpStorm.
 * User: fangaolin
 * Date: 2018/5/26
 * Time: 21:15
 */

namespace app\admin\controller;

use app\admin\model\ProductBis;
use app\admin\model\User as UserModel;
use app\admin\model\Mail as MailModel;

use think\Loader;
use think\Request;

class Examine extends Admin
{

    /**审核商家
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function bis()
    {
        $info = UserModel::getAllBis();
        return $this->fetch('',['info'=>$info]);
    }

    /**商家通过审核
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function passBis()
    {
        if (Request::instance()->isPost()){
            $id = Request::instance()->post('id');
            if ($id){
                $rs = MailModel::pass($id);
                if ($rs){
                    return json(['status'=>1]);
                }else{
                    return json(['status'=>0]);
                }
            }
        }
    }


    /**停用商家账户
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function stopBis()
    {
        if (Request::instance()->isPost()){
            $id = Request::instance()->post('id');
            if ($id){
                $rs = MailModel::stop($id);
                if ($rs){
                    return json(['status'=>1]);
                }else{
                    return json(['status'=>0]);
                }
            }
        }
    }


    /**异步获取地址信息
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAddressImg()
    {
        $id = Request::instance()->post('id');
        if ($id){
            $imgInfo = UserModel::getAddressByBisId($id);
            return json(['status'=>1,'imgSrc'=>$imgInfo['img'],'address'=>$imgInfo['address']]);
        }else
            return json(['status'=>0]);
    }

    /**审核商品
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function product()
    {
        $info = ProductBis::getExamineProduce();
        return $this->fetch('',['info'=>$info]);
    }


    /**商品通过审核
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function passProduce(){
        $id = Request::instance()->post('id');
        if ($id){
            $rs = ProductBis::passExamineProduce($id);
            if ($rs)
                return json(['status'=>1]);
            else
                return json(['status'=>0]);
        }
    }


    /**审核推荐位
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function commend()
    {
        $ProductBis = new ProductBis();
        $examine = $ProductBis->getExamineRecommend();
        return $this->fetch('',['info'=>$examine]);
    }


    /**通过推荐位
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function passRecommend()
    {
        $id = Request::instance()->post('id');
        if ($id){
            $rs = ProductBis::passRecommExamine($id);
            if ($rs)
                return json(['status'=>1]);
            else
                return json(['status'=>0]);
        }
    }



    /**下架推荐位
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function offRecommend()
    {
        $id = Request::instance()->post('id');
        if ($id){
            $rs = ProductBis::offRecommExamine($id);
            if ($rs)
                return json(['status'=>1]);
            else
                return json(['status'=>0]);
        }
    }


    /**彻底下架推荐位
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function deleteRecommed()
    {
        $id = Request::instance()->post('id');
        if ($id){
            $rs = ProductBis::deleteRecommExamine($id);
            if ($rs)
                return json(['status'=>1]);
            else
                return json(['status'=>0]);
        }
    }


}