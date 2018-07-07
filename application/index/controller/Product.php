<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/4/22
 * Time: 11:43
 */

namespace app\index\controller;
use app\common\model\Product as ProductModel;

class Product extends Base
{
    public function index($id)
    {
        if (!intval($id)){
            $this->error('您要访问的商品找不到');
        }
        //记录页面信息
        $this->savePageRecord();
        //调用模型得到数据
        $produModel = new ProductModel();
        $product = $produModel->getProduct($id);
        $param = $produModel->getProductParam($id);
        return $this->fetch('',['product'=>$product,'title'=>$product['name'],'param'=>$param]);
    }
}
