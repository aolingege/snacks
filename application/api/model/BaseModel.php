<?php

namespace app\api\model;

use think\Model;

class BaseModel extends Model
{
    /**拼凑一个图片URL
     * - URL后部分
     * @param $value
     * - 数据信息
     * @param $data
     * - 返回完整的URL信息
     * @return string
     */
    protected function prefixImgAttr($value,$data)
    {
        //拼接URL字段
        if($data['from'] == 1){
            $strNum = strpos($data['url'],"images");
            if ($strNum){
                return config('setting.img_prefix_pc').$value;
            }else{
                return config('setting.img_prefix').$value;
            }
        }
        else
            return $value;
    }
}
