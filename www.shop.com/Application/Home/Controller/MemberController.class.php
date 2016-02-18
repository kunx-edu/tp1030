<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * 会员相关的
 * 会员注册
 * 会员登录
 * 会员退出
 * @author kunx
 */
class MemberController extends \Think\Controller{
    public function register(){
        if(IS_POST){
            $model = D('Member');
            if($model->create()){
                if($model->createMember()!==false){
                    $this->success('注册成功');
                } else{
                    $this->error(get_errors($model->getError()));
                }
            }else{
                $this->error(get_errors($model->getError()));
            }
        }else{
            $this->display();
        }
    }
    /**
     * 验证用户名和邮箱是否已经被占用
     */
    public function checkByField(){
        $username = I('get.username');
        $email = I('get.email');
        $model = D('Member');
        if($username){
            if($model->getByUsername($username)){
                echo 'false';
            }else{
                echo 'true';
            }
        } elseif($email){
            if($model->getByEmail($email)){
                echo 'false';
            }else{
                echo 'true';
            }
        }
    }
    
    
    public function activation($username,$rand){
        $data = array('username'=>$username,'rand'=>$rand);
        if(M('MemberActivation')->where($data)->find()){
            //执行账户激活
            D('Member')->activationMember($username);
            //删除激活记录
            M('MemberActivation')->where($data)->delete();
            $this->success('激活成功，跳转到首页',U('Index/index'));
        } else{
            //提示错误
            $this->error('激活码可能已经失效');
        }
    }
}
