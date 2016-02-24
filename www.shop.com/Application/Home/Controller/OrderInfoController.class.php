<?php

namespace Home\Controller;

/**
 * 描述
 *
 * @author kunx
 */
class OrderInfoController extends \Think\Controller{
    public function index(){
        $model = D('OrderInfo');
        $order_list = $model->getOrderList();
        $this->assign('order_list',$order_list);
        $this->display();
    }
    
    public function receive($sn){
        $model = D('OrderInfo');
        $order_list = $model->receive($sn);
        $this->success('收货完成，请评价',U('index'));
        
    }
}
