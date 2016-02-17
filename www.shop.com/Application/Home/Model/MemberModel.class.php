<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Model;

/**
 * 描述
 *
 * @author kunx
 */
class MemberModel extends \Think\Model {
    protected $patchValidate = true; //开启批量验证
    protected $_validate     = array(
        array('username', '', '用户名不能重复', self::EXISTS_VALIDATE, 'unique'),
        array('username', 'require', '用户名不能为空'),
        array('password', 'require', '密码不能为空'),
        array('email', '', '邮箱不能重复', self::EXISTS_VALIDATE, 'unique'),
        array('email', 'require', '邮箱不能为空'),
        array('email', 'email', '邮箱不合法'),
        array('repassword', 'password', '两次密码不一致', self::EXISTS_VALIDATE, 'confirm'),
        array('password', '6,16', '密码长度不合法', self::EXISTS_VALIDATE, 'length'),
    );
    
    protected $_auto = array(
        array('salt','\Org\Util\String::randString',self::MODEL_INSERT,'function',array(6)),
    );
    
    public function createMember(){
        $this->data['password'] = my_mcrypt($this->data['password'],$this->data['salt']);
        return $this->add();
    }
}
