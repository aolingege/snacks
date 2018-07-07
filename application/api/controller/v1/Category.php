<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/4/6
 * Time: 22:38
 */

namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;

class Category
{
    /**获得所有分类
     * @return false|static[]
     * @throws CategoryException
     * @throws \think\exception\DbException
     */
    public function getAllCategories()
    {
        $categories = CategoryModel::all([],'img');
        if ($categories->isEmpty()){
            throw new CategoryException();
        }
        return $categories;
    }

}