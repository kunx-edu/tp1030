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
class SettingModel extends \Think\Model{
    /**
     * 获取支付方式
     */
    public function getPayList(){
        return $this->getSetting(2);
    }
    
    /**
     * 获取配送方式
     */
    public function getTransportList(){
        return $this->getSetting(1);
    }
    
    /**
     * 根据类型获取配置列表
     * @param type $type
     * @return type
     */
    private function getSetting($type){
        return $this->where('type='.$type)->select();
    }
}
