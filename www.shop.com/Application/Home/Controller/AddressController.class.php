<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * 描述
 *
 * @author kunx
 */
class AddressController extends \Think\Controller{
    public function add(){
        
        $model = D('Address');
        if($model->create()){
            if(($id = $model->addAddress())!==false){
                  $this->success($id); 
                  return;
            } 
        }
        $this->error('添加失败');
    }
    
    /**
     * 返回一个地址的详细记录，json格式
     * @param type $address_id
     */
    public function getAddInfo($address_id){
        $this->ajaxReturn(D('Address')->getAddInfo($address_id));
    }
}
