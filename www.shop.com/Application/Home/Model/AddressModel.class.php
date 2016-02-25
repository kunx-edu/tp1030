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
class AddressModel extends \Think\Model {

    /**
     * 获取用户的地址列表
     * @return type
     */
    public function getAddList() {
        $userinfo  = is_login();
        $member_id = $userinfo['id'];
        $cond      = array('member_id' => $member_id);
        return $this->where($cond)->select();
    }

    /**
     * 新增和修改地址
     * @return boolean
     */
    public function addAddress() {
        $userinfo = is_login();
        if ($this->data['is_default']) {
            $this->where(array('member_id' => $userinfo['id']))->setField('is_default', 0);
        }
        $this->data['member_id'] = $userinfo['id'];
        $id = $this->data['id'];
        //如果表单中的id值不为空，表示是修改
        if ($this->data['id']) {
            if($this->save()!==false){
                return $id;
            }else{
                return false;
            }
        } else {//如果表单中id值为空，表示是新增，并且把id删除
            unset($this->data['id']);
            if (($id = $this->add()) !== false) {
                return $id;
            } else {
                return false;
            }
        }
    }

    /**
     * 获取地址详情
     * @param type $address_id
     * @return type
     */
    public function getAddInfo($address_id) {
        return $this->find($address_id);
    }

}
