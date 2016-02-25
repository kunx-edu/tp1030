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
class OrderInfoModel extends \Think\Model {

    private static $statuses = array(
        0  => '待支付',
        1  => '待发货',
        2  => '待收货',
        3  => '交易完成',
        -1 => '已取消',
    );
    private $shopping_car, $address_info;

    /**
     * 添加订单。
     * @return boolean
     */
    public function addOrder() {

        //获取数据
        $request_data = I('post.');
        //开启事务
        $this->startTrans();
        $this->setShareData($request_data['address_id']);

        /**
         * 解决并发问题
         * 使用redis锁
         */
        if ($this->checkLock() === false) {
            $this->rollback();
            return false;
        }

        /**
         * 减库存
         */
        if ($this->updateStock() === false) {
            $this->rollback();
            $this->freeLock();
            return false;
        }

        //开发票
        if (($invoice_sn = $this->invoiceHandler($request_data)) === false) {
            $this->rollback();
            $this->freeLock();
            return false;
        }

        //插入基本订单信息
        if (($order_info_id = $this->orderInfoHandler($invoice_sn, $request_data)) === false) {
            $this->rollback();
            $this->freeLock();
            return false;
        }

        //插入订单详情
        if ($this->orderInfoItemHandler($order_info_id) === false) {
            $this->rollback();
            $this->freeLock();
            return false;
        }

        //清空购物车
        if (D('ShoppingCar')->flushCar() === false) {
            $this->rollback();
            $this->freeLock();
            return false;
        }
        $this->commit();
        $this->freeLock();
        return true;
    }

    /**
     * redis锁
     */
    private function checkLock() {
        $redis = get_redis();
        for ($i = 0; $i < 3; ++$i) {
            if ($redis->sIsMember(1)) {
                //尝试了三次，都没有解锁，就不在继续，直接返回false
                if ($i == 2) {
                    return false;
                }
                sleep(1);
            } else {
                $redis->sadd('lock_stock', 1);
                break;
            }
        }
        return true;
    }

    /**
     * 释放锁
     */
    private function freeLock() {
        $redis = get_redis();
        $redis->sRem('lock_stock', 1);
    }

    /**
     * 给后续方法准备数据
     * @param type $address_id
     */
    private function setShareData($address_id) {
        $this->shopping_car = D('ShoppingCar')->getCar();
        $this->address_info = D('Address')->getAddInfo($address_id);
    }

    /**
     * 更新库存
     */
    private function updateStock() {
        $shopping_car = $this->shopping_car;
        $goods_ids    = array();
        $update_data  = array();
        $goods_ids    = array_keys($this->shopping_car['goods_list']);
        //查询数据库中商品的库存
        $goods_model  = D('Goods');
        $cond         = array(
            'id' => array('in', $goods_ids),
        );
        /**
         * 判断是否有库存不够的商品
         */
        /**
         * 取得每个商品的库存
         */
        $stocks       = $goods_model->field('id,stock')->where($cond)->select();
        foreach ($stocks as $goods) {
            //检查非法库存
            if ($goods['stock'] < 1) {
                return false;
            }
            //加锁
            if ($this->checkLock($goods['id']) === false) {
                return false;
            }
            $row = array(
                'id'    => $goods['id'],
                'stock' => array('exp', 'stock-' . $this->shopping_car['goods_list'][$goods['id']]['amount']),
            );
            //更新
            if ($goods_model->save($row) === false) {
                $this->freeLock($goods['id']);
                return false;
            }
            //释放锁
            $this->freeLock($goods['id']);
        }
//        /**
//         * 库存都够，就逐个执行修改库存操作，发现失败，直接返回false
//         */
//        foreach ($update_data as $row) {
//            if ($goods_model->save($row) === false) {
//                return false;
//            }
//        }
        return true;
    }

    /**
     * 插入发票
     * @param type $request_data
     * @return type
     */
    private function invoiceHandler($request_data) {
        //创建发票
        //获取购物车内容
        $shopping_car = $this->shopping_car;
        //发票抬头
        $invoice_type = $request_data['receipt_type'];
        if ($invoice_type == 1) {
            $name = $this->address_info['name'];
        } else {
            $name = $request_data['receipt_name'];
        }

        //拼凑发票明细内容
        $intro           = '';
        $receipt_content = $request_data['receipt_content'];
        switch ($receipt_content) {
            case 1:
                //详情，组织数据
                // 张三 
                // 商品1 单价 数量 小计
                foreach ($shopping_car['goods_list'] as $item) {
                    $intro .= $item['name'] . ' ' . $item['shop_price'] . '*' . $item['amount'] . ' ' . $item['sub_total'] . "\r\n";
                }

                break;
            case 2:
                $intro = '办公用品' . "\r\n";
                break;
            case 3:
                $intro = '体育用品' . "\r\n";
                break;
            case 4:
                $intro = '耗材' . "\r\n";
                break;
        }
        // 总金额：
        $intro .= '总计：' . $shopping_car['total_price'] . "\r\n";

        $invoice_intro = $name . "\r\n" . $intro;

        /**
         * [
         *  '内容'=>'张三  体育用品',
         *  'input_time'=>NOW_TIME
         * ]
         */
        $invoice_data = array(
            'intro'      => $invoice_intro,
            'input_time' => NOW_TIME,
        );
        $model        = M('Invoice');
        return $invoice_sn   = $model->add($invoice_data);
    }

    /**
     * 插入订单信息
     * @param type $invoice_sn
     * @param type $request_data
     * @return type
     */
    private function orderInfoHandler($invoice_sn, $request_data) {
        $userinfo = is_login();

        //计算订单号
        $date            = date('Y-m-d');
        $cond            = array('date' => $date);
        $order_num_model = M('OrderNum');
        $order_num       = $order_num_model->where($cond)->getField('num');
        if ($order_num) {
            $order_num++;
            $order_num_model->where($cond)->setInc('num', 1);
        } else {
            $order_num      = 1;
            $order_num_data = array(
                'date' => date('Y-m-d'),
                'num'  => 1,
            );
            $order_num_model->add($order_num_data);
        }


        $order_sn     = 'SN' . date('Ymd') . str_pad($order_num, 10, '0', STR_PAD_LEFT);
        //获取收货信息
        $address_info = $this->address_info;

        //获取出配送方式和名称 价格
        $transport = D('Setting')->getTransportList();
        $transport = get_data_by_column($transport, 'id');
        $pay       = D('Setting')->getPayList();
        $pay       = get_data_by_column($pay, 'id');


        //获取出支付方式和名称
        //得到总的金额
        $order_data = array(
            'sn'             => $order_sn,
            'member_id'      => $userinfo['id'],
            'name'           => $address_info['name'], //收货人姓名
            'province_name'  => $address_info['pname'], //收货人姓名
            'city_name'      => $address_info['cname'], //收货人姓名
            'area_name'      => $address_info['aname'], //收货人姓名
            'detail_address' => $address_info['detail_address'], //收货人姓名
            'tel'            => $address_info['tel'], //收货人姓名
            'invoice_sn'     => $invoice_sn, //发票号码
            'delivery_id'    => $request_data['transport_id'], //配送方式
            'delivery_name'  => $transport[$request_data['transport_id']]['name'], //配送方式名称
            'delivery_price' => $transport[$request_data['transport_id']]['value'], //运费
            'pay_type'       => $request_data['pay_id'], //支付方式
            'pay_name'       => $pay[$request_data['pay_id']]['name'], //支付方式名称
            'price'          => my_number_format($this->shopping_car['total_price'] + $transport[$request_data['transport_id']]['value']), //总价格，包括运费
            'inputtime'      => NOW_TIME,
            'status'         => 0, //待支付
            'trade_no'       => '', //支付宝订单号
        );
        //创建订单信息表数据
        return $this->add($order_data);
    }

    /**
     * 插入订单详情
     * @param type $order_info_id
     * @return type
     */
    private function orderInfoItemHandler($order_info_id) {
        //订单详情表
        $order_info_item_data = array();
        foreach ($this->shopping_car['goods_list'] as $item) {
            $order_info_item_data[] = array(
                'order_info_id' => $order_info_id,
                'goods_id'      => $item['id'],
                'goods_name'    => $item['name'],
                'logo'          => $item['logo'],
                'price'         => $item['shop_price'],
                'amount'        => $item['amount'],
                'total_price'   => $item['sub_total'],
            );
        }
        return D('OrderInfoItem')->addAll($order_info_item_data);
    }

    /**
     * 查询指定用户的订单
     * @return type
     */
    public function getOrderList() {
        $userinfo   = is_login();
        $order_list = $this->where(array('member_id' => $userinfo['id']))->select();
        foreach ($order_list as $key => $item) {
            $item['goods_list'] = D('OrderInfoItem')->field('goods_name,goods_id,logo')->where(array('order_info_id' => $item['id']))->select();
            $item['status_str'] = self::$statuses[$item['status']];
            $order_list[$key]   = $item;
        }
        return $order_list;
    }

    public function doPay($sn) {
        return $this->where(array('sn' => $sn))->setField('status', 1);
    }

    public function receive($sn) {
        return $this->where(array('sn' => $sn))->setField('status', 3);
    }

    /**
     * 删除超时未支付的订单
     * @return type
     */
    public function deleteTimeoutOrder() {
        $cond = array(
            'inputtime' => array('exp', '+900<' . time()),
            'status'=>0,
        );
        return $this->where($cond)->setField('status',-1);//删除已超时的订单
    }

}
