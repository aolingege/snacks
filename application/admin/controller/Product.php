<?php
/**
 * Created by PhpStorm.
 * User: fangaolin
 * Date: 2018/5/27
 * Time: 13:36
 */

namespace app\admin\controller;
use app\admin\model\ProductBis;
use app\common\model\Product as ProductModel;
use app\common\model\Order;
use think\Db;
use think\Request;

class Product extends Admin
{
    /**得到商品
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function product()
    {

        $productModel = new ProductBis();
        $product = $productModel->getAllProduct();
        if (!isset($product))
            $product = [];
        $sum = 0;
        $category = Db::table('category')->select();
        $categoryInfo = [];
        foreach ($category as $row){
            $categoryInfo[$row['id']] = $row['name'];
        }
        $product = $product->toArray();
        foreach ($product as &$row){
            $sum += $row['money'];
            $row['category'] = $categoryInfo[$row['category_id']];
        }
        return $this->fetch('',['product'=>$product,'sum'=>$sum]);
    }


    /**上传商品
     * @param string $id
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function addproduct($id='')
    {
        if (Request::instance()->isPost()){
                $product = Request::instance()->post();
                $productBisModel = new ProductBis();
                if ($productBisModel->saveProductBis($product)){
                    return json(['status'=>1,'info'=>'操作成功']);
                }else
                    return json(['status'=>0,'info'=>'操作失败']);
        }
        $category = Db::table('category')->select();
        if ($id){
            $title = '编辑商品';
            $this->assign('data',Db::table('product_bis')->where(['id'=>$id])->find());
        }else
            $title = '上传商品';
        return $this->fetch('',['category'=>$category,'title'=>$title]);
    }


    /**异步上传商品图片
     * @return \think\response\Json
     */
    public function ajaxUploadProduct()
    {
        $files = Request::instance()->file('file');
        $info = $files->move('images/upload');
        if ($info && $info->getPathname()){
            return json(['status'=>1,'path'=>'/'.str_replace("\\","/",$info->getPathname())]);
        }
        return json(['status'=>0]);
    }


    /**申请产品上线
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function productToOnline()
    {
        $id = Request::instance()->post('id');
        $rs = Db::table('product_bis')->update(['id'=>$id,'status'=>-1]);
        if ($rs){
            return json(['status'=>1]);
        }else
            return json(['status'=>0]);
    }


    /**清空单个产品
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function deleteProduct()
    {
        $id = Request::instance()->post('id');
        $rs = ProductBis::deleteBis($id);
        if ($rs)
            return json(['status'=>1]);
        else
            return json(['status'=>0]);
    }

    /**清空多个产品
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function deleteMultProduct()
    {
        $id = Request::instance()->post();
        $id = $id['id'];
        foreach ($id as $row){
            $rs = ProductBis::deleteBis($row);
            if (!$rs)
                return json(['status'=>0]);
        }
        return json(['status'=>1]);
    }

    
    /**订单展示页面
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function order()
    {
        $info = Order::getAllOrder();
        return $this->fetch('',['info'=>$info]);
    }

    /**更改订单为发货状态
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function deliver()
    {
        if (Request::instance()->isAjax()){
            $id = Request::instance()->post('id');
            $rs = Order::deliver($id);
            if ($rs){
                return json(['status'=>1]);
            }else{
                return json(['status'=>0]);
            }
        }
    }


    /**得到所有推荐位
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRecommend()
    {
        if (Request::instance()->isAjax()){
            $rs = Db::table('recommended')->select();
            $rsInfo = [];
            foreach ($rs as $row){
                $rsInfo[] = ['describe'=>$row['describe'],'url'=>$row['img_url'],'id'=>$row['id']];
            }
            return json(['status'=>1,'info'=>json_encode($rsInfo)]);
        }
    }


    /**申请推荐位
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function putRecommend()
    {
        $id = Request::instance()->post('id');
        $recom = Request::instance()->post('recom');
        if ($id && $recom){
            $rs = ProductBis::putRecomm($recom,$id);
            if ($rs)
                $this->success('您已经成功申请了推荐位');
            else
                $this->error('申请失败，请再试下');
        }else{
            $this->error('请选择一个推荐位');
        }
    }


    /**管理推荐位
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function recomm()
    {
        $ProductBisModel = new ProductBis();
        $recommend = $ProductBisModel->getExamineRecommend('all');
        if (!$recommend)
            $recommend = [];
        return $this->fetch('',['info'=>$recommend]);
    }


    /**在线商品
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function onlineProduct()
    {
        $allProduct = ProductModel::getOnlineGoods();
        if (!$allProduct)
            $allProduct = [];
        return $this->fetch('',['product'=>$allProduct]);
    }


    /**删除在线产品
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function deleteOnlineProduct()
    {
        if (Request::instance()->isAjax()){
            $id = Request::instance()->post('id');
            $rs = ProductBis::deleteOnline($id);
            if ($rs){
                return json(['status'=>1]);
            }else{
                return json(['status'=>0]);
            }
        }
    }


    /**清空多个在线产品库存
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public  function clearMultOnlineProduct()
    {
        $id = Request::instance()->post();
        $id = $id['id'];
        foreach ($id as $row){
            $rs = ProductBis::clearOnline($row);
            if (!$rs)
                return json(['status'=>0]);
        }
        return json(['status'=>1]);
    }


    /**清空在线产品
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public  function clearOnlineProduct()
    {
        if (Request::instance()->isAjax()){
            $id = Request::instance()->post('id');
            $rs = ProductBis::clearOnline($id);
            if ($rs){
                return json(['status'=>1]);
            }else{
                return json(['status'=>0]);
            }
        }
    }


    /**查看收件人地址
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function seeAddress()
    {
        if (Request::instance()->isAjax()){
            $id = Request::instance()->post('id');
            $productBis = new ProductBis();
            $rs = $productBis->seeAddress($id);
            if ($rs){
                return json(['status'=>1,'img'=>$rs['img'],'address'=>$rs['address']]);
            }else{
                return json(['status'=>0]);
            }
        }
    }

}