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
class LocationController extends \Think\Controller{
    private $_model;
    protected function _initialize(){
        $this->_model = D('Locations');
    }
    public function index(){
        $provinces = $this->_model->getChildrenByParentId(0);
        $this->assign('provinces',$provinces);
        $this->assign('userinfo',  is_login());
        //获取用户现有的收货地址
        $address_model = D('Address');
        $addresses = $address_model->getAddList();
        $this->assign('addresses',  $addresses);
        
        $this->display();
    }
    
    
    public function getChildrenByParentId($parent_id){
        $provinces = $this->_model->getChildrenByParentId($parent_id);
        $this->ajaxReturn($provinces);
    }
}
