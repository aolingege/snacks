<?php
/**
 * Created by PhpStorm.
 * User: fangaolin
 * Date: 2018/4/24
 * Time: 11:35
 */
namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Express;
use app\admin\model\Lottery;

class Service extends Controller
{

    public function service()
    {
        $express = new Express();
        $result = [];
        $comSelect = '';
        if (Request::instance()->isPost()){
            $comSelect = Request::instance()->post('com');
            $id = Request::instance()->post('id');
            if ($comSelect && $id){
                $result = $express->query($comSelect,$id);
                $result = $result['result'];
            }
        }
        $com = $express->getExpress();
        if (empty($result)){
            $flag = false;
            if (isset($id)){
                $this->assign('tips','单号不存在或者已经过期、请稍后再试！');
            }
        }else{
            $flag = true;
        }
        return $this->fetch('',['com'=>$com,'result'=>$result,'flag'=>$flag,'comSelect'=>$comSelect]);
    }


    public function lottery()
    {
        $lotteryModel = new Lottery();
        $flag = false;
        $result = [];
        if (Request::instance()->isPost()){
            $lottery = Request::instance()->post('type');
            if (isset($lottery)){
                $result = $lotteryModel->getLotteryHistroyRes($lottery,20);
                $result = $result['result']['lotteryResList'];
                $flag = true;
            }
        }
        $resultType = $lotteryModel->getType();
        return $this->fetch('',['type'=>$resultType,'flag'=>$flag,'result'=>$result]);
    }


}