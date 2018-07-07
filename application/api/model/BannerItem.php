<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/6
 * Time: 9:41
 */

namespace app\api\model;


class BannerItem extends BaseModel
{
    //隐藏字段
    protected $hidden = ['img_id','update_time','delete_time','banner_id'];

    /**
     * 返回关联数据模型
     * @return \think\model\relation\BelongsTo
     */
    public function img()
    {
        return $this->belongsTo('image','img_id','id');
    }

}