<?php

namespace app\api\model;

class Theme extends BaseModel
{
    //隐藏的字段
    protected $hidden = ['delete_time','update_time','topic_img_id','head_img_id'];

    /**
     * 返回img数据
     * @return \think\model\relation\BelongsTo
     */
    public function topicImg()
    {
        return $this->belongsTo('image','topic_img_id','id');
    }

    /**
     * 返回img数据
     * @return \think\model\relation\BelongsTo
     */
    public function headImg()
    {
        return $this->belongsTo('image','head_img_id','id');
    }

    /**主题关联的产品
     * 返回关联模型
     * @return \think\model\relation\BelongsToMany
     */
    public function products()
    {
        //主题与产品多对多中间关联
        return $this->belongsToMany('Product','theme_product','product_id','theme_id');
    }

    /**得到关联的产品信息，顶部图片和头部图片
     * 传入主题ID
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getThemeWithProducts($id)
    {
        $themes = self::with(['products','topicImg','headImg'])->find($id);
        return $themes;
    }
}
