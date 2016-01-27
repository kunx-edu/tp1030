<?php 
namespace Admin\Model;

/**
 *
 * @author kunx
 */
class ArticleModel extends \Think\Model\RelationModel{
    protected $patchValidate = true;//开启批量验证
    protected $_validate = array(
        array('name','require','名称不能为空'),
	array('article_category_id','require','文章分类不能为空'),
	array('status','require','状态@radio|1=是&0=否不能为空'),
	array('sort','require','排序不能为空'),
	array('inputtime','require','录入时间不能为空')
    );
    
    protected $_auto = array(
        array('inputtime',NOW_TIME),
    );
    
    protected $_link = array(
        'ArticleContent'=>array(
            'mapping_type'=>self::HAS_ONE,
            'foreign_key'=>'article_id',
        ),
//        'GoodsArticle'=>array(
//            
//        ),
        
    );
    /**
     * @param int|array $id
     * @param int $status
     */
    public function changeStatus($id,$status=-1) {
        //如果id不是数组，就转换成数组，以便后面使用统一的tp的where数组格式。
        if(!is_array($id)){
            $id = array($id);
        }
        //1.如果status是-1，那么就将原来的名字后添加_del
        if($status==-1){
            $data['name'] = array('exp','concat(name,"_del")');
        }
        $data['status']=$status;
        //2.执行数据的更新操作。
        return D('Brand')->where(array('id'=>array('in',$id)))->setField($data);
    }
    
    /**
     * 新闻入库
     * 基本信息在本模型对应的数据表中
     * 详细信息放在了article_content表中
     */
    public function createArticle(){
        unset($this->data['id']);
        if(($id = $this->add())!==false){
            $data = array(
                'article_id'=>$id,
                'content'=>I('post.content','',false),
            );
            if(M('ArticleContent')->add($data)!==false){
                return true;
            }
        }
        return false;
    }
    
    /**
     * 保存文章的时候，将详细内容存入到content表中
     * @return boolean
     */
    public function modifyArticle(){
        $request_data = $this->data;
        if(parent::save()!==false){
            $data = array(
                'article_id'=>$request_data['id'],
                'content'=>I('post.content','',false),
            );
            if(M('ArticleContent')->save($data)!==false){
                return true;
            }
        }
        return false;
    }
    
    public function getArticles($goods_id){
        //SELECT a.id,a.name FROM article AS a LEFT JOIN goods_article AS ga ON a.`id`=ga.`article_id` WHERE goods_id=5
        return $model = $this->field('a.id,a.name')->alias('a')->join('left join goods_article as ga ON a.`id`=ga.`article_id`')->where(array('goods_id'=>$goods_id))->select();
    }
}
