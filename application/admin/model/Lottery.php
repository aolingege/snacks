<?php
/**
 * Created by PhpStorm.
 * User: fangaolin
 * Date: 2018/4/26
 * Time: 15:47
 */
namespace app\admin\model;

use think\Config;
use think\Model;

class Lottery extends Model
{
    /**
     * 获取支持彩票列表
     */
    public  function getLotteryTypes()
    {
        $urlPath = '/types';
        $params = [
            'key' => Config::get('juhe.lotteryKey')
        ];
        $paramsString = http_build_query($params);
        $requestUrl = Config::get('juhe.lotteryUrl').$urlPath;
        $content = mixCurl($requestUrl, $paramsString);
        $result = json_decode($content, true);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }


    /**
     * 获取彩票某一期开奖结果,默认最新一期
     * @param $lotteryId 彩票ID
     * @param string $lotteryNo 彩票期数，默认最新一期
     * @return bool|mixed
     */
    public  function getLotteryRes($lotteryId, $lotteryNo = "")
    {
        $urlPath = '/query';
        $params = [
            'key' => Config::get('juhe.lotteryKey'),
            'lottery_id' => $lotteryId,
            'lottery_no' => $lotteryNo
        ];
        $paramsString = http_build_query($params);
        $requestUrl = Config::get('juhe.lotteryUrl').$urlPath;
        $content = mixCurl($requestUrl, $paramsString);
        $result = json_decode($content, true);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }


    /**获取历史开奖结果
     * @param $lotteryId 彩票ID
     * @param int $pageSize 每页返回条数
     * @param int $page 当前页数
     * @return bool|mixed
     */
    public function getLotteryHistroyRes($lotteryId, $pageSize = 10, $page = 1)
    {
        $urlPath = '/history';
        $params = [
            'key' => Config::get('juhe.lotteryKey'),
            'lottery_id' => $lotteryId,
            'page_size' => $pageSize,
            'page' => $page,
        ];
        $paramsString = http_build_query($params);
        $requestUrl = Config::get('juhe.lotteryUrl').$urlPath;
        $content = mixCurl($requestUrl, $paramsString);
        $result = json_decode($content, true);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }


    /**
     * 中奖计算器/判断号码是否中奖
     * @param $lotteryId 彩票ID
     * @param $lotteryRes 投注号码
     * @param string $lotteryNo 投注期号，默认最新一期
     * @return bool|mixed
     */
    public  function getBonus($lotteryId, $lotteryRes, $lotteryNo='')
    {
        $urlPath = '/bonus';
        $params = [
            'key' => Config::get('juhe.lotteryKey'),
            'lottery_id' => $lotteryId,
            'lottery_res' => $lotteryRes,
            'lottery_no' => $lotteryNo,
        ];
        $paramsString = http_build_query($params);
        $requestUrl = Config::get('juhe.lotteryUrl').$urlPath;
        $content = mixCurl($requestUrl, $paramsString);
        $result = json_decode($content, true);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }



    public function getType(){
        $resultType = cache('lotteryType');
        if ($resultType){
            $resultType = json_decode($resultType,true);
        }else{
            $resultType = $this->getLotteryTypes();
            cache('lotteryType',json_encode($resultType));
        }
        return $resultType['result'];
    }



}


//https://www.cnblogs.com/zhuchenglin/p/6554801.html