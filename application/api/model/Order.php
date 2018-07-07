<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/10
 * Time: 20:14
 */

namespace app\api\model;


class Order extends BaseModel
{
    protected $hidden = ['user_id','delete_time','update_time'];
    protected $autoWriteTimestamp = true;

    public function getSnapItemsAttr($value)
    {
        if (empty($value)){
            return null;
        }
        return json_decode($value);
    }

    public function getSnapAddressAttr($value)
    {
        if (empty($value)){
            return null;
        }
        return json_decode($value);
    }

    /**根据uid分页查询
     * @param $uid
     * @param int $page
     * @param int $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function getSummaryByUser($uid,$page =1,$size = 15)
    {
            $pagingDate = self::where('user_id','=',$uid)
                ->order('create_time desc')
                ->paginate($size,true,['page'=>$page]);
            return $pagingDate;
    }

    /**所有分页查询
     * @param int $page
     * @param int $size
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public static function getSummaryByPage($page=1, $size=20){
        $pagingData = self::order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
        return $pagingData ;
    }

}