<?php

namespace app\api\model;

class Product extends BaseModel
{
    protected $hidden = [
        'delete_time','pivot','from','create_time','update_time'
    ];

    /**拼凑完整的URL
     * URL后部分
     * @param $value
     * URL数据模型
     * @param $data
     * URL信息
     * @return string
     */
    public function getMainImgUrlAttr($value,$data)
    {
        return $this->prefixImgAttr($value,$data);
    }

    /**得到产品信息
     * 请求产品的数量
     * @param $count
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getMostRecent($count)
    {
            $products = self::limit($count)->order('create_time DESC')->select();
            return $products;
    }

    /**
     * 通过分类ID去查找对应的商品
     * 分类ID
     * @param $categoryID
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getProductsByCategoryID($categoryID)
    {
        $products = self::where('category_id','=',$categoryID)->select();
        return $products;
    }

    /**
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getProductDetail($id)
    {

            $product = self::with(['imgs'=>function($query){
                $query->with(['imgUrl'])->order('order','asc');
            }])->with(['properties'])->find($id);
            return $product;
    }

    /**产品对应的详情页数据模型
     * @return \think\model\relation\HasMany
     */
    public function imgs()
    {
        return $this->hasMany('productImage','product_id','id');
    }

    /**产品对应的详细参数的数据模型
     * @return \think\model\relation\HasMany
     */
    public function properties()
    {
        return $this->hasMany('ProductProperty','product_id','id');
    }

}
