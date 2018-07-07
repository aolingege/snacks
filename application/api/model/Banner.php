<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/5
 * Time: 14:46
 */

namespace app\api\model;


class Banner extends BaseModel
{
    //模型查询数据时调用子类的多态去隐藏字段
    protected $hidden = ['update_time','delete_time'];

    /**通过自身数据模型（banner）去关联其他模型，形成连锁的信息
     * @return \think\model\relation\HasMany
     */
    public function items()
    {
        return $this->hasMany('BannerItem','banner_id','id');
    }

    /**
     * 通过ID得到banner信息
     * banner的ID
     * @param $id
     * 成功查询到数据时返回数据对象
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getBannerByID($id){
            $result = self::with(['items','items.img'])->find($id);
            return $result;
    }
}