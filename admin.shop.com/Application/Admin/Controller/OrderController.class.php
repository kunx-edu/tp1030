<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;

/**
 * 描述
 *
 * @author kunx
 */
class OrderController extends \Think\Controller{
    public function index(){
        $model = D('OrderInfo');
        $this->assign('order_list',$model->getList());
        $this->display();
    }
    
    
    public function send(){
        //接收sn参数
        $sn = I('get.sn');
        //改变订单状态
        $model = D('OrderInfo');
        $model->send($sn);
        $this->success('发货成功',U('index'));
        //告诉给支付宝说，我们已经发货了
    }
}
