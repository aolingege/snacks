<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/5/20
 * Time: 18:28
 */

namespace app\index\controller;

use app\common\model\Category as CategoryModel;
use app\common\model\Product as ProductModel;
use think\Request;

class Category extends Base
{

    /**分类展示
     * @param string $id
     * @param string $type
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index($id='all',$type='all')
    {
        $Categroy = new CategoryModel();
        //得到分类的信息
        $info = $Categroy->getCategory('');
        $product = new ProductModel();
        $all = $product->getAllGoods($id,$type);
        $count = count($all);
        return $this->fetch('',['title'=>'零食分类','category'=>$info,
            'all'=>$all,'count'=>$count,'type'=>$type,'id'=>$id]);
    }

}


