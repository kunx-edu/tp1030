<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Model;

/**
 * token相关的数据
 *
 * @author kunx
 */
class AdminTokenModel extends \Think\Model {

    /**
     * 插入token
     * @param type $admin_id
     * @param type $token
     */
    public function addToken($admin_id, $token) {
        $this->deleteToken($admin_id);
        $data = array(
            'admin_id' => $admin_id,
            'token'    => $token,
        );
        $this->add($data);
    }

    /**
     * 删除token
     * @param type $admin_id
     */
    public function deleteToken($admin_id) {
        $this->where(array('admin_id' => $admin_id))->delete();
    }

    /**
     * 自动登录
     */
    public function checkToken($admin_id, $token) {
        $data = array(
            'admin_id' => $admin_id,
            'token'    => $token,
        );
        if ($this->where($data)->find()) {
            //如果token验证通过，就返回用户信息
            //如果token验证通过，我们还是要存用户信息到session中，所以我们在这个方法中一步完成
            $userinfo = D('Admin')->find($admin_id);
            session('USERINFO', $userinfo);
            //保存token到数据库和cookie
            cookie('admin_id', $admin_id, 604800); //保存一周
            $token    = create_token();
            cookie('token', $token, 604800); //保存一周
            D('AdminToken')->addToken($admin_id, $token);
        } else {
            return false;
        }
    }

}
