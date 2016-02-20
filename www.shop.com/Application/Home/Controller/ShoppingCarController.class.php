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
        //1.判断是否登录
        $userinfo = is_login();
        if($userinfo){
            $model = D('ShoppingCar');
            $car = $model->getCarList($userinfo['id']);
            $car = get_data_by_column($car,'goods_id');
            if(isset($car[$goods_id])){
//                $car[$goods_id] += $amount;
                $amount += $car[$goods_id];
            }else {
//                $car[$goods_id] = $amount;
            }
            $car = array(
                'goods_id'=>$goods_id,
                'amount'=>$amount,
                'member_id'=>$userinfo['id'],
            );
            $model->addCar($car);
        }else {
        //1.1.如果没有登录，就存到cookie中
            /**
             * goods_id  amount
             *  1          3
             *  2          4
             */
            //1.1.1取出原来的，判断是否存在相同商品，如果有，加数，如果没有加元素
            $car = unserialize(cookie('SHOPPING_CAR'));
            if(isset($car[$goods_id])){
                $car[$goods_id] += $amount;
                cookie('SHOPPING_CAR',  serialize($car));
            }else {
                $car[$goods_id] = $amount;
                cookie('SHOPPING_CAR',  serialize($car));
            }
            
        }
    }
    
    //购物车列表
    public function flow1(){
        var_dump(I('post.'));
        
    }
}
