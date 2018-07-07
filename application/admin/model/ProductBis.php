<?php
/**
 * Created by PhpStorm.
 * User: fangaolin
 * Date: 2018/4/26
 * Time: 15:47
 */
namespace app\admin\model;

use think\Db;
use think\Loader;
use think\Model;
use think\Session;
Loader::import('baiduMap.Map',EXTEND_PATH,'.php');
class ProductBis extends Model
{

    protected $autoWriteTimestamp = true;

    /**得到当前商家的商品
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getAllProduct()
    {
        $admin = Session::get('admin','Login');
        $account =  $admin['user_name'];
        $bisInfo = Db::table('bis')->where(['account'=>$account])->find();
        if ($bisInfo)
            return Db::table('product_bis')->where(['bis_id'=>$bisInfo['id']])->select();
    }


    /**保存商品信息
     * @param $product
     * @return bool|int|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function saveProductBis($product)
    {
        if ($product){
            $bisInfo = $this->getBisID();
            if ($bisInfo){
                if (isset($product['id']) && $product['id'] != ''){
                    //编辑
                    foreach ($product as $key => $row){
                        if ($row == ''){
                            unset($product[$key]);
                        }
                    }
                    Db::table('product_bis')->update($product);
                    $productInfo = Db::table('product_bis')->where(['id'=>$product['id']])->find();
                    if (isset($productInfo['status']) && $productInfo['status'] == 1){
                        unset($product['id']);
                        $product['id'] = $productInfo['product_id'];
                        return Db::table('product')->update($product);
                    }else
                        return true;
                }
                $product['from'] =  1;
                $product['count'] =  0;
                $product['money'] =  0;
                $product['status'] =  0;
                $product['product_id'] = -1;
                $product['bis_id'] = $bisInfo['id'];
                $product['img_id'] = Db::table('image')->insertGetId(['url'=>$product['main_img_url'],'from'=>1]);
                unset($product['id']);
                $rs = Db::table('product_bis')->insertGetId($product);

                if ($rs)
                    return $rs;
                else
                    return false;
            }else
                return false;
        }
    }

    /**得到商户ID
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBisID()
    {
        $admin = Session::get('admin','Login');
        $account =  $admin['user_name'];
        return Db::table('bis')->where(['account'=>$account])->find();

    }


    /**清除库存
     * @param $id
     * @return int|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function deleteBis($id)
    {
        if ($id){
            $rs = Db::table('product_bis')->where(['id'=>$id])->find();
            if (isset($rs['status']) && $rs['status'] == 1){
                    Db::table('product')->where(['id'=>$rs['product_id']])->update(['stock'=>0]);
            }
            return Db::table('product_bis')->update(['stock'=>0,'id'=>$id]);
        }
    }


    /**得到审核产品
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getExamineProduce()
    {
        $examine = Db::table('product_bis')->where(['status'=>-1])->select();
        $category = Db::table('category')->select();
        $categoryInfo = [];
        foreach ($category as $row){
            $categoryInfo[$row['id']] = $row['name'];
        }
        $examine = $examine->toArray();
        foreach ($examine as &$row){
            $bis = $row['bis_id'];
            $bisInfo = Db::table('bis')->where(['id'=>$bis])->find();
            $row['bis'] = $bisInfo['name'];
            $row['category'] = $categoryInfo[$row['category_id']];
        }
        return $examine;
    }

    /**商品通过审核
     * @param $id
     * @return bool|int|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function passExamineProduce($id)
    {
        if ($id){
            $product = Db::table('product_bis')->where(['id'=>$id])->find();
            if ($product){
                $productOnline['name'] = $product['name'];
                $productOnline['price'] = $product['price'];
                $productOnline['stock'] = $product['stock'];
                $productOnline['category_id'] = $product['category_id'];
                $productOnline['main_img_url'] =  str_replace("/images","",$product['main_img_url']);
                $productOnline['from'] = $product['from'];
                $productOnline['summary'] = $product['summary'];
                $productOnline['img_id'] = $product['img_id'];
                $productUpdate['product_id'] = Db::table('product')->insertGetId($productOnline);
                $productUpdate['status'] = 1;
                $productUpdate['id'] = $product['id'];
                $bisInfo = Db::table('bis')->where(['id'=>$product['bis_id']])->find();
                $content = "<h2>恭喜您，您的审核产品：".$productOnline['name']."已经通过审核并上线</h2><br/>";
                $content .= "<h3>后台管理账号:{$bisInfo['account']}</h3>";
                \phpmailer\Email::send($bisInfo['email'],'商品审核通过通知',$content);
                return Db::table('product_bis')->update($productUpdate);
            }else
                return false;
        }else
            return false;
    }


    /**申请到推荐位
     * @param $recommID
     * @param $productID
     * @return bool|int|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function putRecomm($recommID,$productID)
    {
        $data['location_id'] = $recommID;
        $product = Db::table('product_bis')->where(['id'=>$productID])->find();
        if ($product){
            $data['product_id'] = $product['product_id'];
            $data['status'] = 0;
            $data['create_time'] = time();
            return Db::table('recommend_item')->insertGetId($data);
        }else
            return false;
    }


    /**得到审核中的推荐位
     * @param string $status
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getExamineRecommend($status = '')
    {
        if ($status == '')
            $examine = Db::table('recommend_item')->where(['status'=>0])->select();
        elseif($status == 'all')
            $examine = Db::table('recommend_item')->select();
        $allExamine = Db::table('recommended')->select();
        $allProduct = Db::table('product')->select();
        $productInfo = [];
        foreach ($allProduct as $row){
            $productInfo[$row['id']] = $row['name'];
        }
        $examineInfo = [];
        foreach ($allExamine as $row){
            $examineInfo[$row['id']] = $row['describe'];
        }
        $examine = $examine->toArray();
        foreach ($examine as &$row){
            $row['product_name'] = $productInfo[$row['product_id']];
            $row['recomm_name'] = $examineInfo[$row['location_id']];
            $row['create_time'] = date('Y-m-d H:i:s',$row['create_time']);
        }
        return $examine;
    }


    /**通过推荐位审核
     * @param $id
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function passRecommExamine($id)
    {
        if ($id){
            $recommInfo = Db::table('recommend_item')->where(['id'=>$id])->find();
            $recommInfo['status'] = 1;
            $update = Db::table('recommend_item')->update($recommInfo);
            if ($update){
                $productID = $recommInfo['product_id'];
                $productMore = self::getProductBisByID($productID);
                $updateRs = Db::table('product_bis')->where(['id'=>$productMore['id']])->update(['status'=>2]);
                if ($updateRs){
                    $bisInfo = self::getBisInfoByID($productMore['bis_id']);
                    $content = "<h2>恭喜您，您的审核产品：".$productMore['name']." 已经通过审核并添加到推荐位</h2><br/>";
                    $content .= "<h3>后台管理账号:{$bisInfo['account']}</h3>";
                    \phpmailer\Email::send($bisInfo['email'],'推荐位审核通过通知',$content);
                    return true;
                }
            }else
                return false;
        }else
            return false;
    }


    /**通过ID得到商家信息
     * @param $id
     * @return array|bool|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getBisInfoByID($id)
    {
        if ($id)
            return Db::table('bis')->where(['id'=>$id])->find();
        else
            return false;
    }

    /**通过产品ID得到商家产品
     * @param $id
     * @return array|bool|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getProductBisByID($id)
    {
        if ($id)
            return Db::table('product_bis')->where(['product_id'=>$id])->find();
        else
            return false;
    }


    /**推荐位下架
     * @param $id
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function offRecommExamine($id)
    {
        if ($id){
            $recommInfo = Db::table('recommend_item')->where(['id'=>$id])->find();
            $recommInfo['status'] = 0;
            $update = Db::table('recommend_item')->update($recommInfo);
            if ($update){
                $productID = $recommInfo['product_id'];
                $productMore = self::getProductBisByID($productID);
                $updateRs = Db::table('product_bis')->where(['id'=>$productMore['id']])->update(['status'=>1]);
                if ($updateRs){
                    $bisInfo = self::getBisInfoByID($productMore['bis_id']);
                    $content = "<h2>对不起，您推荐位的产品：".$productMore['name']."被下架,我们将会进一步审核该产品</h2><br/>";
                    $content .= "<h3>后台管理账号:{$bisInfo['account']}</h3>";
                    \phpmailer\Email::send($bisInfo['email'],'推荐位下架审核通知',$content);
                    return true;
                }
            }else
                return false;
        }else
            return false;
    }

    /**彻底取消推荐位
     * @param $id
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function deleteRecommExamine($id)
    {
        if ($id){
            $recommInfo = Db::table('recommend_item')->where(['id'=>$id])->find();
            $update = Db::table('recommend_item')->where(['id'=>$recommInfo['id']])->delete();
            if ($update){
                $productID = $recommInfo['product_id'];
                $productMore = self::getProductBisByID($productID);
                $updateRs = Db::table('product_bis')->where(['id'=>$productMore['id']])->update(['status'=>1]);
                $bisInfo = self::getBisInfoByID($productMore['bis_id']);
                if ($updateRs){
                    $content = "<h2>对不起，您推荐位的产品：".$productMore['name']."被下架</h2><br/>";
                    $content .= "<h3>后台管理账号:{$bisInfo['account']}</h3>";
                }else{
                    $content = "<h2>对不起，您申请的推荐位的产品：".$productMore['name']."未通过审核</h2><br/>";
                    $content .= "<h3>后台管理账号:{$bisInfo['account']}</h3>";
                }
                \phpmailer\Email::send($bisInfo['email'],'推荐位下架审核通知',$content);
                return true;
            }else
                return false;
        }else
            return false;
    }


    /**删除在线商品
     * @param $id
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function deleteOnline($id)
    {
        $deleteRs = Db::table('product')->where(['id'=>$id])->delete();
        if ($deleteRs){
            $rs = self::getProductBisByID($id);
            if ($rs){
                $rs['status'] = 0;
                $updateRs = Db::table('product_bis')->update($rs);
                $bis = self::getBisInfoByID($rs['bis_id']);
                if ($updateRs && $bis){
                    $content = "<h2>对不起，您的产品：".$rs['name']."被下架,我们将进一步审核该产品。</h2><br/>";
                    $content .= "<h3>后台管理账号:{$bis['account']}</h3>";
                    \phpmailer\Email::send($bis['email'],'产品下架通知',$content);
                }
                return true;
            }else
                return true;
        }else
            return false;
    }

    /**清除在线删除的库存
     * @param $id
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function clearOnline($id)
    {
        $deleteRs = Db::table('product')->where(['id'=>$id])->update(['stock'=>0]);
        if ($deleteRs){
            $rs = self::getProductBisByID($id);
            if ($rs){
                $rs['stock'] = 0;
                $updateRs = Db::table('product_bis')->update($rs);
                $bis = self::getBisInfoByID($rs['bis_id']);
                if ($updateRs && $bis){
                    $content = "<h2>对不起，您的产品：".$rs['name']."被暂停销售，请自行检查问题后自行开始销售。</h2><br/>";
                    $content .= "<h3>后台管理账号:{$bis['account']}</h3>";
                    \phpmailer\Email::send($bis['email'],'产品暂停销售通知',$content);
                }
                return true;
            }else
                return true;
        }else
            return false;
    }


    /**查看地址
     * @param $id
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function seeAddress($id)
    {
        if ($id){
            $order = $this->getOrder($id);
            $address = json_decode($order['snap_address'],true);
            if (isset($address['country']))
                $address = $address['province'].$address['city'].$address['country'].$address['detail'];
            else
                $address = $address['province'].$address['city'].$address['detail'];
            return ['img'=>\Map::getStaticimageSrc($address),'address'=>$address];
        }else
            return false;
    }


    /**得到订单信息
     * @param $id
     * @return array|bool|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrder($id)
    {
        if ($id)
            return Db::table('order')->where(['id'=>$id])->find();
        else
            return false;
    }



}