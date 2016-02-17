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
        $url = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
        //忽略验证的请求
        $ignore = C('IGNORE_PATH');
        if(in_array($url, $ignore)){
            return true;
        }
        //1.判断用户是否登录，如果登录了才能得到用户id，进而才有可能获取到所拥有的权限
        $userinfo = session('USERINFO');
        if ($userinfo) {
            //获取用户角色对应的权限
            $sql            = 'SELECT DISTINCT `permission_id` FROM admin_role AS ar LEFT JOIN role_permission AS rp ON ar.`role_id`=rp.`role_id` WHERE ar.`admin_id`=' . $userinfo['id'];
            $rows           = M()->query($sql);
            $pids1          = array_column($rows, 'permission_id');
            $sql            = 'SELECT DISTINCT permission_id FROM admin_permission AS ap WHERE ap.`admin_id`=' . $userinfo['id'];
            $rows           = M()->query($sql);
            $pids2          = array_column($rows, 'permission_id');
            //取出相同的和不同的，然后合并就得到了所有的权限id
            $pids_intersect = array_intersect($pids1, $pids2);
            //取出不同的
            $pids_diff      = array_diff($pids1, $pids2);
            $pids           = array_merge($pids_intersect, $pids_diff);

            //根据权限id获取到对应的path
            $pids_str = implode(',', $pids);
            $sql      = "SELECT DISTINCT path FROM permission WHERE id IN ($pids_str) AND path !=''";
            $rows     = M()->query($sql);
            $paths    = array_column($rows, 'path');
            //将权限id和path存到session中
            session('permission_ids', $pids);
            session('paths', $paths);
            
            
            //获取用户可以看到的菜单
            $sql = 'SELECT DISTINCT `path`,`name`,`level` FROM menu_permission AS mp LEFT JOIN menu AS m ON m.`id`=mp.`menu_id` WHERE permission_id IN ('.$pids_str.') ORDER BY lft ASC';
            $menus     = M()->query($sql);
            var_dump($menus);
        }
        
//        echo $url;
//        var_dump($paths);
//        exit;
        //判断是否有权限，如果没有权限就判断是否登录，登录了就提示切换用户，否则跳到登录页面
        if(!in_array($url, $paths)){
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
