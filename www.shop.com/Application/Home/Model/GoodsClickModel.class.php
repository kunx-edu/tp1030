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
class GoodsClickModel extends \Think\Model {
    public function userClick($goods_id){
        $redis = get_redis();
        $key = 'goods_clicks';
        return $redis->zincrby($key,1,$goods_id);
        
        $cond = array('goods_id'=>$goods_id);
        if($this->where($cond)->find()){
            $this->where('goods_id='.$goods_id)->setInc('click_times'); // 点击次数+1
        }else {
            $data = array('goods_id'=>$goods_id,'click_times'=>1);
            $this->add($data);
        }
        
//        [
//            100=>10000,
//            101=>100,
//            102=>1,
//        ];
         //获取商品的点击次数
        return $this->where($cond)->getField('click_times');
    }
    
    public function getAllClicks(){
        $redis = get_redis();
        $key = 'goods_clicks';
        return $redis->zRange($key, 0, -1, true); 
//        return $redis->zincrby($key,1,$goods_id);
    }
    
    public function sync(){
        //获取redis中所有的点击数
        $clicks  = $this->getAllClicks();
        //获取所有的商品id
        $goods_ids = array_keys($clicks);
        //1.找出数据库中没有记录的商品
        //1.1取出数据库中所有的商品id
        $db_goods_ids = $this->getField('goods_id',true);
        
        //取出数据库中有，redis中也有的，进行删除再添加
        
        $exist_ids = array_intersect($goods_ids, $db_goods_ids);
        if($exist_ids){
            $this->where(array('goods_id'=>array('in',$exist_ids)))->delete();
        }
        $data = array();
        foreach($clicks as $goods_id=>$click){
            $data[] = array('goods_id'=>$goods_id,'click_times'=>$click);
        }
        $this->addAll($data);
//        exit;
        
        
//        var_dump($exist_ids);
//        exit;
//        
//        
//        $not_exist_goods_id = array_diff($goods_ids, $db_goods_ids);
//        //阻止要新增的数据
//        $add_data = array();
//        foreach($not_exist_goods_id as $value){
//            $add_data[] = array('goods_id'=>$value,'click_times'=>$clicks[$value]);
//            unset($clicks[$value]);
//        }
//        $this->addAll($add_data);
//        
//        //更新已经存在点击次数
//        $update_data = array();
//        $update_ids = array();
//        foreach($clicks as $goods_id=>$click){
//            $update_data[] = array('goods_id'=>$goods_id,'click_times'=>$click);
//            $update_ids[] =$goods_id;
//        }
//        $this->where(array('goods_id'=>array('in',$update_ids)))->delete();
//        //增加
//        $this->addAll($update_data);
//        
//        var_dump($add_data);
    }
}
