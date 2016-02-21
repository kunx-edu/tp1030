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
class ShoppingCarController extends \Think\Controller{
    public function addGoods($goods_id,$amount){
        $model = D('ShoppingCar');
        $car = array(
            'goods_id'=>$goods_id,
            'amount'=>$amount,
        );
        $model->addCar($car);
        $this->redirect('flow1');
    }
    
    //购物车列表
    public function flow1(){
        $model = D('ShoppingCar');
        $car_list = $model->getCar();
        $this->assign('car_list',$car_list);
        $this->display();
        
    }
    
    public function flow2(){
        if(!is_login()){
            cookie('__forward__',__SELF__);
            $this->error('请先登录',U('Member/login'));
        }else{
            echo '正常流程';
        }
    }
    
    
    public function syncCar($goods_id,$amount){
        $model = D('ShoppingCar');
        $model->syncCar($goods_id,$amount);
    }
}
