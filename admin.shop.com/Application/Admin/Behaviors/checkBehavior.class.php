<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Behaviors;

/**
 * 此行为用于验证用户是否拥有访问的权限
 *
 * @author kunx
 */
class checkBehavior extends \Think\Behavior {

    public function run(&$params) {
        
        //1.判断用户是否登录，如果登录了才能得到用户id，进而才有可能获取到所拥有的权限
//        $userinfo = session('USERINFO');
        $userinfo = login();
        //自动登录
        $admin_id = cookie('admin_id');
        $token = cookie('token');
        if(!$userinfo){
            //自动登录
            D('AdminToken')->checkToken($admin_id, $token);
//            $userinfo = session('USERINFO');
            $userinfo = login();
        }
        $url = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
        //忽略验证的请求
        $ignore = C('IGNORE_PATH');
        if(in_array($url, $ignore)){
            return true;
        }
        return true;
        //判断是否有权限，如果没有权限就判断是否登录，登录了就提示切换用户，否则跳到登录页面
        if(!in_array($url, path())){
            if($userinfo){
                echo '无权访问，<a href="'.U('Admin/Admin/login').'">切换用户</a>';
                exit;
            }else{
                redirect(U('Admin/Admin/login'),'请先登录');
                exit;
            }
        }
    }

}
