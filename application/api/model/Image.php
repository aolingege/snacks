<?php

namespace app\api\model;

class Image extends BaseModel
{
    //隐藏字段
    protected $hidden = ['id','from','delete_time','update_time'];

    /**
     * 框架自动调用
     * URL后半部分
     * @param $value
     * img数据模型
     * @param $data
     * @return string
     */
    public function getUrlAttr($value,$data)
    {
        return $this->prefixImgAttr($value,$data);
    }



}
