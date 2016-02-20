<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    
    public function _initialize(){
        $this->assign('action_name', ACTION_NAME);
        $this->assign('meta_title', '京西商城首页');
        $all_categories = S('all_categories');
        if(!$all_categories){
            //获取所有分类，并且展示出来
            $all_categories = D('GoodsCategory')->getList('id,parent_id,name,level');
            S('all_categories',$all_categories);
        }
        $this->assign('all_categories',$all_categories);
        
        //获取帮助文章分类，获取每个分类下，最多6篇文章
        $help_categories = S('help_categories');
        if(!$help_categories){
            //1.创建模型
            $model           = D('ArticleCategory');
            //2.获取数据
            $where['status'] = array('gt', 0);
            $where['is_help'] = 1;

            $help_categories = $model->where($where)->select();
            S('help_categories',$help_categories);
        }
        $help_articles = S('help_articles');
        if(!$help_articles){
            $model           = D('Article');
            $help_articles = $model->getHelpArticles();
            $help_articles_tmp = array();
            foreach($help_articles as $article){
                $help_articles_tmp[$article['article_category_id']][] = $article;
            }
            $help_articles = $help_articles_tmp;
            S('help_articles',$help_articles);
        }
        $this->assign('help_categories',$help_categories);
        $this->assign('help_articles',$help_articles);
        
        $this->assign('userinfo',is_login());
        //获取对应的文章
//        var_dump($help_articles);
        /**
         * 3=>[
         *  [第一篇文章],
         *  [第二篇文章],
         * ]
         */
        
    }
    
    public function index(){
        $model = D('Goods');
        $best_goods = $model->getGoodsByStatus(1);
        $new_goods = $model->getGoodsByStatus(2);
        $hot_goods = $model->getGoodsByStatus(4);
        $this->assign('best_goods', $best_goods);
        $this->assign('new_goods', $new_goods);
        $this->assign('hot_goods', $hot_goods);
       
//        var_dump($all_categories);
        $this->display();
    }
    
    public function test(){
        $this->display();
    }
    
    /**
     * 展示商品详情。
     * @param type $goods_id
     */
    public function goods($goods_id){
        $model = D('Goods');
        $goods = $model->getGoodsDetail($goods_id);
        if(!$goods){
            $this->error('你看的商品已经离家出走了，请选择其它的吧',U('index'));
        } else{
            $this->assign('goods', $goods);
            //取出商品对应的图片
    //        $gallery = D('GoodsGallery')->field('path')->where(array('goods_id'=>$goods_id))->select();
    //        $gallery = array_column($gallery, 'path');
    //        $this->assign('gallery', $gallery);
    //        var_dump($gallery);
            $this->display();
        }
    }
}