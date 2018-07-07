<?php
namespace app\common\model;

use app\api\model\Image as ImageModel;
use think\Db;
use think\Model;
use think\Request;

class Product extends Model
{

    protected $hidden = ['delete_time','update_time','create_time'];

    /**得到产品信息
     * @param $id
     * @return array|bool|null|static
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function getProduct($id)
    {
        $product = self::get($id);
        $category = new Category();
        if (!$product)
            return false;
        //分类信息
        $category = $category->getCategoryOne($product->category_id);
        if (!$category)
            return false;
        $category->hidden(['description','topic_img_id','id']);
        $category = $category->toArray();
        $product->hidden(['img_id','category_id']);
        $product = $product->toArray();
        $product['category'] = $category['name'];
        return $product;
    }


    /**通过产品ID得到产品参数
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getProductParam($id)
    {
        $temp = Db::table('product_image')->where(['product_id'=>$id])
            ->order('order','ASC')->select();
        $imgUrl = [];
        foreach ($temp->toArray() as $row){
            $img = ImageModel::get($row['img_id']);
            $imgUrl[] = $img->url;
        }
        $info['img'] = $imgUrl;
        $temp = Db::table('product_property')->where(['product_id'=>$id])->select();
        $info['property'] = $temp->toArray();
        return $info;
    }


    /**过滤URL信息
     * @param $value
     * @param $data
     * @return string
     */
    public function getMainImgUrlAttr($value,$data)
    {

        if($data['from'] == 1)
            return config('setting.img_prefix').$value;
        else
            return $value;
    }


    /**根据分类得到所有商品
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Collection|static[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllGoods($id,$type)
    {
        $info = [];
        $where = "1=1";
        if ($id != 'all'){
            $where = "category_id= '".$id."'";
        }
        if ($type){
            switch ($type){
                case 'time':  $info = self::where($where)->order('create_time DESC')->select();break;
                case 'price': $info = self::where($where)->order('price DESC')->select();break;
                case 'all': $info = self::where($where)->select();break;
            }
        }
        return $info;
    }


    /**得到所有在线商品用于管理员
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getOnlineGoods()
    {
        //得到所有的分类
        $category = Db::table('category')->select();
        $categoryInfo = [];
        foreach ($category as $row){
            $categoryInfo[$row['id']] = $row['name'];
        }
        $allGoods = Db::table('product')->select();
        $allGoods = $allGoods->toArray();
        foreach ($allGoods as &$row){
            $row['category'] = $categoryInfo[$row['category_id']];
        }
        return $allGoods;
    }


    /**获得首页推荐商品
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllRecommend()
    {
        $mainInfo = Db::table('recommended')->where(['id'=>1])->find();
        if ($mainInfo){
           $rs = Db::table('recommend_item')->where(['location_id'=>$mainInfo['id']])->select();
           $rs = $rs->toArray();
           foreach ($rs as $key =>&$row){
               $product = Db::table('product')->where(['id'=>$row['product_id']])->find();
               $row['name'] = $product['name'];
               $row['price'] = $product['price'];
               $row['stock'] = $product['stock'];
               $row['id'] = $product['id'];
               $row['main_img_url'] = '/images'.$product['main_img_url'];
               $row['summary'] = $product['summary'];
               if ($key == 4)
                   break;
           }
           if(isset($key) && $key < 4){
               //推荐位商品不足
               $productCount = 4 - $key;
               for ($i = 0;$i < $productCount;$i++){
                   $id = intval(rand(1,30));
                   $productAdd = Db::table('product')->where(['id'=>$id])->find();
                   if ($productAdd){
                       $productAdd['main_img_url'] = "/images".$productAdd['main_img_url'];
                       $rs[++$key] = $productAdd;
                   }else
                       $i--;
               }
           }
           return $rs;
        }else
            return [];
    }

}