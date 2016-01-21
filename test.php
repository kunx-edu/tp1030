<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
header('Content-Type: text/html;charset=utf-8');
$reg = '/([^\|]*)(\|(radio|text)\|(.*))?/';
$str = array(
    '状态|radio|1=启用&0=隐藏',
    '名字','类别|text',
);

foreach($str as $item){
    preg_match_all($reg, $item,$matches);
    var_dump($matches);
    unset($matches);
}