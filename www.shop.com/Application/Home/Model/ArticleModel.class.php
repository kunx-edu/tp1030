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
class ArticleModel extends \Think\Model{
    /**
     * 获取帮助文章
     * @return type
     */
    public function getHelpArticles(){
        $cond = array(
            'ac.is_help'=>1,
            'ac.status'=>1,
            'a.status'=>1,
        );
        return $this->field('a.`id`,a.`name`,a.`article_category_id`')->alias('a')->join('article_category as ac ON a.`article_category_id`=ac.`id`')->where($cond)->select();
    }
}
