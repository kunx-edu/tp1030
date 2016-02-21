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
class LocationsModel extends \Think\Model{
    public function getChildrenByParentId($parent_id){
        $rows = $this->field('id,name')->where(array('parent_id'=>$parent_id))->select();
        return $rows;
    }
}
