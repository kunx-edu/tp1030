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
                }
            }else{
                $this->error(get_errors($model->getError()));
            }
        }else{
            $this->display();
        }
    }
}
