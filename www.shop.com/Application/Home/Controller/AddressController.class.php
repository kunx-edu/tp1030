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
    
    public function getAddInfo($address_id){
        $this->ajaxReturn(D('Address')->find($address_id));
    }
}
