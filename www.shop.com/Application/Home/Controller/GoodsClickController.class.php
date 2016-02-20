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
class GoodsClickController extends \Think\Controller{
    //增加点击次数
    public function click($goods_id){
        $model = D('GoodsClick');
        //找到该条记录，判断是否有没有
        $clicks = $model->userClick($goods_id);
        $this->ajaxReturn($clicks);
    }
    
    public function sync(){
        set_time_limit(0);//永不超时
        $model = D('GoodsClick');
//        $rst = $model->getAllClicks();
        $model->sync();
        echo '<script type="text/javascript">window.close();</script>';
//        var_dump($rst);
    }
}
