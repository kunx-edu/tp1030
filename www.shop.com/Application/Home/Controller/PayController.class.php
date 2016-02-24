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
class PayController extends \Think\Controller{
    public function index(){
        $sn = I('get.sn');
        $model = D('OrderInfo');
        $model->doPay($sn);
        echo 'success';
        return true;
        $this->display();
    }
}
