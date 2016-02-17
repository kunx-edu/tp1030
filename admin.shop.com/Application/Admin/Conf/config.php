<?php

define('DOMAIN', 'http://admin.shop.com/');
return array(
//'配置项'=>'配置值'
    'TMPL_PARSE_STRING' => array(
        '__CSS__' => DOMAIN . 'Public/css',
        '__JS__'  => DOMAIN . 'Public/js',
        '__IMG__' => DOMAIN . 'Public/img',
        '__EXT__' => DOMAIN . 'Public/ext',
    ),
    /* 数据库设置 */
    'DB_TYPE'           => 'mysql', // 数据库类型
    'DB_HOST'           => '127.0.0.1', // 服务器地址
    'DB_NAME'           => 'tp1030', // 数据库名
    'DB_USER'           => 'root', // 用户名
    'DB_PWD'            => '123456', // 密码
    'DB_PORT'           => '3306', // 端口
    'DB_PREFIX'         => '', // 数据库表前缀
//    'DB_PARAMS'         => array(
//        \PDO::ATTR_CASE => \PDO::CASE_LOWER,
//    ), // 数据库连接参数
    'DB_DEBUG'          => TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DB_CHARSET'        => 'utf8', // 数据库编码默认采用utf8
    'SHOW_PAGE_TRACE'   => TRUE,
    'PAGE_SIZE'         => 4,
    'PAGE_THEME'        => '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%',
    'UPLOAD_PATH'       => APP_PATH . 'Uploads/',
    //无需验证就可看到的路径
    'IGNORE_PATH'       => array(
        'Admin/Admin/login',
        'Admin/Captcha/captcha',
        'Admin/Index/index',
        'Admin/Index/top',
        'Admin/Index/menu',
        'Admin/Index/main',
    ),
    'COOKIE_PREFIX'=>'xianrentiao_',
);
