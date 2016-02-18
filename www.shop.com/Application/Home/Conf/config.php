<?php
define('DOMAIN', 'http://www.shop.com/');
return array(
	//'配置项'=>'配置值'
    'TMPL_PARSE_STRING' => array(
        '__CSS__' => DOMAIN . 'Public/css',
        '__JS__'  => DOMAIN . 'Public/js',
        '__IMG__' => DOMAIN . 'Public/img',
        '__EXT__' => DOMAIN . 'Public/ext',
    ),
    
    'EMAIL_CONF'=>array(
        'host'=>'smtp.126.com',
        'username'=>'kunx_edu@126.com',
        'password'=>'iam4ge',
        'author'=>'sige',//显示的发件人名字
    ),
);