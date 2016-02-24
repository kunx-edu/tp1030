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
class ShoppingCarController extends \Think\Controller {

    public function addGoods($goods_id, $amount) {
        $model = D('ShoppingCar');
        $car   = array(
            'goods_id' => $goods_id,
            'amount'   => $amount,
        );
        $model->addCar($car);
        $this->redirect('flow1');
    }

    //购物车列表
    public function flow1() {
        $model    = D('ShoppingCar');
        $car_list = $model->getCar();
        $this->assign('car_list', $car_list);
        $this->display();
    }

    public function flow2() {
        if (!is_login()) {
            cookie('__forward__', __SELF__);
            $this->error('请先登录', U('Member/login'));
            return;
        }

        if (IS_POST) {
            $order_model = D('OrderInfo');
            if($order_model->addOrder()){
                $this->redirect('flow3');
            }else{
                $this->error('下单失败，请稍后再试');
            }
        } else {
            //用户地址列表
            $this->assign('address_list', D('Address')->getAddList());
            //获取支付方式
            $this->assign('pay_list', D('Setting')->getPayList());
            //获取配送方式
            $this->assign('transport_list', D('Setting')->getTransportList());

            //取出购物车里面的内容
            $this->assign('car_list', D('ShoppingCar')->getCar());
            $this->display();
        }
    }

    public function syncCar($goods_id, $amount) {
        $model = D('ShoppingCar');
        $model->syncCar($goods_id, $amount);
    }

    public function flow3(){
        $this->display();
    }
}
