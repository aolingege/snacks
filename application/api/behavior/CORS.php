<?php
/**
 * Created by PhpStorm.
 * User: FangAolin
 * Date: 2018/4/18
 * Time: 16:29
 */

namespace app\api\behavior;

class CORS
{
    /**允许应用的POST和GET请求跨域
     * @param $params
     */
    public function appInit(&$params)
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: POST,GET');
        if(request()->isOptions()){
            exit();
        }
    }
}