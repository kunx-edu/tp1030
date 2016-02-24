<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Model;

/**
 * 描述
 *
 * @author kunx
 */
class OrderInfoModel extends \Think\Model{
    public function getList(){
        return $this->select();
    }
    
    
    public function send($sn){
        return $this->where(array('sn'=>$sn))->setField('status',2);
    }
}
