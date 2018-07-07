<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/5
 * Time: 11:14
 */

namespace app\api\controller\v1;

use app\api\validate\IDMustBePostiveInt;
use app\api\model\Banner as BannerModel;
use app\lib\exception\BannerMissException;

class Banner
{


    /**
     * @url /banner/:id
     * @http GET
     * banner的ID号
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws BannerMissException
     * @throws \app\lib\exception\ParameterException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBanner($id)
    {
        //调用父类方法验证ID
        $validate = new IDMustBePostiveInt();
        $validate->goCheck();
        $banner = BannerModel::getBannerByID($id);
        if (!$banner){
            throw new BannerMissException();
        }
        return $banner;
    }
}