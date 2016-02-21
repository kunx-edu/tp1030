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
class ShoppingCarModel extends \Think\Model {

    public function getCarList($member_id) {
        $cond = array('memeber_id' => $member_id);
        return $this->where($cond)->select();
    }

    /**
     * 将购物车存入数据库
     * @param type $car
     */
    public function addCar($car,$is_sync=false) {
        $userinfo = is_login();
        $amount = $car['amount'];
        $goods_id = $car['goods_id'];
        if($userinfo){
            $model = D('ShoppingCar');
            $car = $model->getCarList($userinfo['id']);
            $car = get_data_by_column($car,'goods_id');
            //如果数据表中已经有了这个商品，并且当前是添加到购物车，而不是在购物车页面修改数量
            if(isset($car[$goods_id]) && !$is_sync){
                $amount += $car[$goods_id]['amount'];
            //如果是在购物车页面更新数量
            }elseif($is_sync) {
                $car[$goods_id] = $amount;
            }
            $car = array(
                'goods_id'=>$goods_id,
                'amount'=>$amount,
                'member_id'=>$userinfo['id'],
            );
            
            //删除用户对应的此商品记录
            $cond = $car;
            unset($cond['amount']);
            $this->where($cond)->delete();
            if($amount){
                $this->add($car);
            }
            
//            $model->addCar($car);
        }else {
        //1.1.如果没有登录，就存到cookie中
            /**
             * goods_id  amount
             *  1          3
             *  2          4
             */
            //1.1.1取出原来的，判断是否存在相同商品，如果有，加数，如果没有加元素
            $car = cookie_shopping_car();
            //如果购物车中有这个商品
            //如果是同步页面，就直接复制
            if($is_sync){
                $car[$goods_id] = $amount;
            }else{
                if(isset($car[$goods_id])){
                    $car[$goods_id] += $amount;
                }else{
                    $car[$goods_id] = $amount;
                }
            }
            
            if(!$car[$goods_id]){
                unset($car[$goods_id]);
            }
            cookie_shopping_car($car);
            
        }
    }

    /**
     * 将cookie中的商品保存到数据库中，并且销毁cookie
     * cookie :
     * 1=>4
     */
    public function moveCarToDb() {
        //取出cookie中的购物车数据
        $cookie_car = cookie_shopping_car();
        //取出数据库中购物车数据
        $userinfo   = is_login();
        $member_id  = $userinfo['id'];
        $cond       = array(
            'member_id' => $member_id,
        );
        $db_car     = $this->field('goods_id,amount')->where($cond)->select();
        $db_car     = get_data_by_column($db_car, 'goods_id');
        //遍历所有的cookie中的数据，如果数据库中有，就加数量，如果没有就加记录
        if ($cookie_car) {
            //取出数据库中的所有商品id
            $db_goods_ids     = array_keys($db_car);//4 1
            $cookie_goods_ids = array_keys($cookie_car);//1
            //求出两个的交集
            $same_ids         = array_intersect($db_goods_ids, $cookie_goods_ids);
            //取出两个购物车中相同商品的数量，并求和
            $update_data      = array();
            foreach ($same_ids as $id) {
                $update_data[] = array(
                    'goods_id'   => $id,
                    'amount'     => $cookie_car[$id] + $db_car[$id]['amount'],
                    'member_id' => $member_id,
                );
            }
            $diff_ids    = array_diff($cookie_goods_ids, $db_goods_ids);
            $insert_data = array();
            foreach ($diff_ids as $id) {
                $insert_data[] = array(
                    'goods_id'   => $id,
                    'amount'     => $cookie_car[$id],
                    'member_id' => $member_id,
                );
            }

            $data = array_merge($update_data, $insert_data);
            //删除数据库存在的相同的商品记录
            if($same_ids){
                $cond = array(
                    'goods_id'  => array('in', $same_ids),
                    'member_id' => $member_id,
                );
                $this->where($cond)->delete();
            }
            $this->addAll($data);
            //删除cookie中的购物车
            cookie_shopping_car(array());
        }
    }
    
    /**
     * 获取购物车的商品信息，及其数量
     * @return array
     */
    public function getCar(){
        $userinfo = is_login();
        if($userinfo){
            $car = $this->getCarList($userinfo['id']);
            $car = get_data_by_column($car, 'goods_id');
            $data = array();
            foreach($car as $item){
                $data[$item['goods_id']] = $item['amount'];
            }
            $car = $data;
        }else{
            $car = cookie_shopping_car();
        }
        $total_price = 0;
        if($car){
            //取出各商品的详细信息
            $goods_ids = array_keys($car);
            $goods_model = D('Goods');
            $cond = array('id'=>array('in',$goods_ids));
            $goods_info = $goods_model->field('id,name,shop_price,logo')->where($cond)->select();
            $goods_info = get_data_by_column($goods_info, 'id');
            $data = array();
            foreach($car as $goods_id=>$amount){
                $item = $goods_info[$goods_id];
                $item['amount'] = $amount;
                $item['sub_total'] = my_number_format($item['shop_price'] * $item['amount']);
                $total_price += $item['sub_total'];
                $data[$goods_id] = $item;
            }
            $total_price = my_number_format($total_price);
        } else{
            $data = array();
        }
        $data = array(
            'total_price'=>$total_price,
            'goods_list'=>$data
        );
        return $data;
    }
    
    public function syncCar($goods_id,$amount){
        $car = array('goods_id'=>$goods_id,'amount'=>$amount);
        $this->addCar($car,true);
    }

    
}
