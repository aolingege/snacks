<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/4/22
 * Time: 11:43
 */

namespace app\index\controller;
use app\common\model\Product;

class Index extends Base
{


    /**首页展示
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $this->savePageRecord();
        $productModel = new Product();
        $recommend = $productModel->getAllRecommend();
        return $this->fetch('',['recommend'=>$recommend]);
    }


}


