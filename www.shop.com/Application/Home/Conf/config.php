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
);