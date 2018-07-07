<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/5
 * Time: 15:17
 */

namespace app\lib\exception;


use Exception;
use think\exception\Handle;
use think\Log;
use think\Request;

class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;

    /**重写TP框架的异常渲染
     * 传入的异常信息
     * @param Exception $e
     * 抛出异常（自定义异常，框架异常）
     * @return \think\Response|\think\response\Json
     */
    public function render(\Exception $e)
    {
        if($e instanceof BaseException){
            //自定义异常
            $this->code = $e->code;
            $this->msg  = $e->msg;
            $this->errorCode  = $e->errorCode;
        }else{

            //读取配置文件,判断是否是调试模式

            if(config('app_debug')){
                return parent::render($e);
            }else{
                //生产模式抛出的异常
                $this->code = 500;
                $this->msg  = '对不起，服务器走神出错啦。';
                $this->errorCode  = 999;
                $this->recordErrorLog($e);
            }
        }
        //获得HTTP请求信息
        $request = Request::instance();
        $result = [
            'msg'=> $this->msg,
            'error_code' => $this->errorCode,
            'request_url' => $request->url()
        ];
        return json($result,$this->code);
    }

    /**日志写入
     * 传入的异常
     * @param Exception $e
     */
    private function recordErrorLog(Exception $e)
    {
        Log::init([
            'type' => 'File',
            'path' => LOG_PATH,
            'level' => ['error']
        ]);
        Log::record($e->getMessage(),'error');
    }
}