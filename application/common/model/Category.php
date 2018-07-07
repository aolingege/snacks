<?php
namespace app\common\model;

use think\Db;
use think\Model;

class Category extends Model
{
    protected  $autoWriteTimestamp = true;

    protected $hidden = ['delete_time','update_time'];

    /**得到多条分类的信息
     * 条数
     * @param $count
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCategory($count)
    {
        $categoryInfo = self::limit($count)->select();
        $categoryInfo = $categoryInfo->toArray();
        foreach ($categoryInfo as &$row){
            $info = Db::table('product')->where(['category_id'=>$row['id']])->select();
            foreach ($info as $key=>$vo){
                $strNum = strpos($vo['main_img_url'],"images");
                if ($strNum){
                    $vo['main_img_url'] = config('setting.img_prefix_pc').$vo['main_img_url'];
                }else{
                    $vo['main_img_url'] = config('setting.img_prefix').$vo['main_img_url'];
                }
                $info[$key] = $vo;
            }
            $row['data'] = $info;
        }
        return $categoryInfo;
    }


    /**根据分类ID得到分类信息
     * @param $id
     * @return bool|null|static
     * @throws \think\exception\DbException
     */
    public function getCategoryOne($id)
    {
        if (intval($id))
            return self::get($id);
        else
            return false;
    }




}