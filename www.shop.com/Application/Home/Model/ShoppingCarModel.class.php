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
    public function addCar($car) {
        //删除用户对应的此商品记录
        $cond = $car;
        unset($cond['memeber_id']);
        $this->where($cond)->delete();
        $this->add($car);
    }

    /**
     * 将cookie中的商品保存到数据库中，并且销毁cookie
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
            $db_goods_ids     = array_keys($db_car);
            $cookie_goods_ids = array_keys($cookie_car);
            //求出两个的交集
            $same_ids         = array_intersect($db_goods_ids, $cookie_goods_ids);
            //取出两个购物车中相同商品的数量，并求和
            $update_data      = array();
            foreach ($same_ids as $id) {
                $update_data[] = array(
                    'goods_id'   => $id,
                    'amount'     => $cookie_car[$id] + $db_car[$id]['amount'],
                    'memeber_id' => $member_id,
                );
            }
            $diff_ids    = array_diff($cookie_goods_ids, $db_goods_ids);
            $insert_data = array();
            foreach ($diff_ids as $id) {
                $insert_data[] = array(
                    'goods_id'   => $id,
                    'amount'     => $cookie_car[$id],
                    'memeber_id' => $member_id,
                );
            }

            $data = array_merge($update_data, $insert_data);
            var_dump($data);
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
        if($car){
            //取出各商品的详细信息
            $goods_ids = array_keys($car);
            $goods_model = D('Goods');
            $cond = array('id'=>array('in',$goods_ids));
            $goods_info = $goods_model->field('id,name,shop_price')->where($cond)->select();
            $goods_info = get_data_by_column($goods_info, 'id');
            $data = array();
            foreach($car as $goods_id=>$amount){
                $item = $goods_info[$goods_id];
                $item['amount'] = $amount;
                $data[$goods_id] = $item;
            }
        } else{
            $data = array();
        }
        return $data;
    }

}
