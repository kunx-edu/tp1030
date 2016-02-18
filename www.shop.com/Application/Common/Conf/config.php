<?php

return array(
    //'配置项'=>'配置值'
    /* 数据库设置 */
    'DB_TYPE'    => 'mysql', // 数据库类型
    'DB_HOST'    => '127.0.0.1', // 服务器地址
    'DB_NAME'    => 'tp1030', // 数据库名
    'DB_USER'    => 'root', // 用户名
    'DB_PWD'     => '123456', // 密码
    'DB_PORT'    => '3306', // 端口
    'DB_PREFIX'  => '', // 数据库表前缀
//    'DB_PARAMS'         => array(
//        \PDO::ATTR_CASE => \PDO::CASE_LOWER,
//    ), // 数据库连接参数
    'DB_DEBUG'   => TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DB_CHARSET' => 'utf8', // 数据库编码默认采用utf8
    
    //Redis Session配置
    'SESSION_AUTO_START'    =>  true,    // 是否自动开启Session
    'SESSION_TYPE'            =>  'Redis',    //session类型
    'SESSION_PERSISTENT'    =>  1,        //是否长连接(对于php来说0和1都一样)
    'SESSION_CACHE_TIME'    =>  1,        //连接超时时间(秒)
    'SESSION_EXPIRE'        =>  0,        //session有效期(单位:秒) 0表示永久缓存
    'SESSION_PREFIX'        =>  'sess_',        //session前缀
    'SESSION_REDIS_HOST'    =>  '127.0.0.1', //分布式Redis,默认第一个为主服务器
    'SESSION_REDIS_PORT'    =>  '6379',           //端口,如果相同只填一个,用英文逗号分隔
    'SESSION_REDIS_AUTH'    =>  '',    //Redis auth认证(密钥中不能有逗号),如果相同只填一个,用英文逗号分隔
);
