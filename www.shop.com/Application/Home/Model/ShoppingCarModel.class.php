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
class ShoppingCarModel extends \Think\Model{
    public function getCarList($member_id){
        $cond = array('memeber_id'=>$member_id);
        return $this->where($cond)->select();
    }
    
    /**
     * 将购物车存入数据库
     * @param type $car
     */
    public function addCar($car){
        //删除用户对应的此商品记录
        $cond = $car;
        unset($cond['memeber_id']);
        $this->where($cond)->delete();
        $this->add($car);
    }
}
