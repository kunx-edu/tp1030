<?php

namespace Home\Model;

/**
 * 描述
 *
 * @author kunx
 */
class GoodsModel extends \Think\Model{
    /**
     * 获取指定状态的商品
     * @param type $status
     * @param type $limit
     */
    public function getGoodsByStatus($status,$limit=5){
        $where = 'goods_status & '.$status;
        return $this->where($where)->where(array('status'=>1))->limit($limit)->select();
    }
    
    
    public function getGoodsDetail($goods_id){
        $where = array(
            'id'=>$goods_id,
            'status'=>1,
        );
        $goods_info =  $this->where($where)->find();
//        if(!$goods_info){
//            return false;
//        }
        $gallery = D('GoodsGallery')->field('path')->where(array('goods_id'=>$goods_id))->select();
        $gallery = array_column($gallery, 'path');
//        if(!$gallery){
//            return false;
//        }
        $goods_info['gallery'] = $gallery;
        
//        $intro = M('GoodsIntro')->field('content')->where(array('goods_id'=>$goods_id))->find();
        $intro = M('GoodsIntro')->getFieldByGoodsId($goods_id,'content');;
//        if(!$intro){
//            return false;
//        }
        $goods_info['intro']=$intro;
        
        return $goods_info;
    }
}
