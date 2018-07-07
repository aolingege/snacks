<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/4/18
 * Time: 15:12
 */

namespace app\api\model;



class ThirdApp extends BaseModel
{
    public static function check($ac, $se)
    {
        $app = self::where('app_id','=',$ac)
            ->where('app_secret', '=',$se)
            ->find();
        return $app;

    }
}