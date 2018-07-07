<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    'view_replace_str'       => [
        '__STATIC__'=> '/static',
    ],
    //验证码配置
    'captcha'  => [
        // 验证码字符集合
        'codeSet'  => '123456789abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',
        // 验证码字体大小(px)
        'fontSize' => 24,
        // 是否画混淆曲线
        'useCurve' => true,
        // 验证码图片高度
        //'imageH'   => 30,
        // 验证码图片宽度
        //'imageW'   => 100,
        // 验证码位数
        'length'   => 4,
        // 验证成功后是否重置
        'reset'    => true
    ],
    'template' => [
        'layout_on' => true,
        'layout_name' => 'layout',
        'layout_item' => '{__CONTENT__}'
    ],
    // 默认输出类型
    'default_return_type'    => 'html',
];
