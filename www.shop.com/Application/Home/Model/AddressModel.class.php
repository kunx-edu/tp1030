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
class AddressModel extends \Think\Model{
    public function getAddList(){
        $userinfo = is_login();
        $member_id = $userinfo['id'];
        $cond = array('member_id'=>$member_id);
        return $this->where($cond)->select();
    }
    
    public function addAddress(){
        $userinfo = is_login();
        if($this->data['is_default']){
            $this->where(array('member_id'=>$userinfo['id']))->setField('is_default',0);
        }
        $this->data['member_id'] = $userinfo['id'];
        if(($id = $this->add())!==false){
            return $id;
        }else{
            return false;
        }
    }
}
