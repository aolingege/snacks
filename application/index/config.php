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
    // 默认输出类型
    'default_return_type'    => 'html',
    //模板文件
    'template' => [
        'layout_on' => true,
        'layout_name' => 'layout',
        'layout_item' => '{__CONTENT__}'
    ],
    'view_replace_str'       => [
        '__STATIC__'=> '/static',
    ],
];
