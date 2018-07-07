<?php
/**
 * Created by PhpStorm.
 * User: fangaolin
 * Date: 2018/5/29
 * Time: 11:39
 */
namespace app\admin\model;

use think\Config;
use think\Model;

class Express extends Model
{

    /**
     * 返回支持的快递公司公司列表
     * @return array
     */
    public function getComs(){
        $params = 'key='.Config::get('juhe.expressKey');
        $content = mixCurl(Config::get('juhe.expressComUrl'),$params);
        return $this->_returnArray($content);
    }

    public function query($com,$no){
        $params = array(
            'key' => Config::get('juhe.expressKey'),
            'com'  => $com,
            'no' => $no
        );
        $content = mixCurl(Config::get('juhe.expressUrl'),$params,1);
        return $this->_returnArray($content);
    }
    /**
     * 将JSON内容转为数据，并返回
     * @param string $content [内容]
     * @return array
     */
    public function _returnArray($content){
        return json_decode($content,true);
    }



    public function getExpress(){
        $com = cache('expressCom');
        if (!$com){
            $slectData = $this->getComs();
            if ($slectData['resultcode'] == 200){
                $com = $slectData['result'];
                cache('expressCom', json_encode($com));
            }
        }else{
            $com = json_decode($com,true);
        }
        return $com;
    }

}