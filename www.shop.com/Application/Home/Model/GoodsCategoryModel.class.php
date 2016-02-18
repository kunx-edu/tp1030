<?php

namespace Home\Model;

/**
 * 描述
 *
 * @author kunx
 */
class GoodsCategoryModel extends \Think\Model{
    public function getList($field = '*'){
        $where = array('status'=>1);
        return $this->field($field)->where($where)->order('lft')->select();
    }
}
