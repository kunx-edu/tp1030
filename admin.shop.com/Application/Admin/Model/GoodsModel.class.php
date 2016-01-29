<?php

namespace Admin\Model;

/**
 *
 * @author kunx
 */
class GoodsModel extends \Think\Model\RelationModel {

    /**
     * 商品状态配置数组
     * @var type 
     */
    public static $statuses = array(
        1 => '精品',
        2 => '新品',
        4 => '热销',
    );

    /**
     * 商品上下架配置。
     * @var type 
     */
    public static $isOnSale  = array(
        1 => '在售',
        0 => '下架',
    );
    protected $patchValidate = true; //开启批量验证
    protected $_validate     = array(
        array('name', 'require', '名称不能为空'),
//	array('sn','require','货号不能为空'),
//        array('logo', 'require', '商品LOGO不能为空'),
        array('goods_category_id', 'require', '商品分类不能为空'),
        array('brand_id', 'require', '品牌不能为空'),
        array('supplier_id', 'require', '供货商不能为空'),
        array('market_price', 'require', '市场价格不能为空'),
        array('shop_price', 'require', '本店价格不能为空'),
        array('stock', 'require', '库存不能为空'),
//	array('goods_status','require','商品状态不能为空'),
        array('is_on_sale', 'require', '是否上架不能为空'),
        array('status', 'require', '状态@radio|1=是&0=否不能为空'),
        array('sort', 'require', '排序不能为空'),
    );

    /**
     * 调用create的时候，自动完成的操作
     * @var type 
     */
    protected $_auto = array(
        array('goods_status', 'array_sum', 3, 'function'), //对于goods_status字段进行求并集运算
        array('inputtime', NOW_TIME), //插入的时候自动设定当前时间
    );

    /**
     * 关联模型
     */
    protected $_link = array(
        'GoodsIntro'   => array(
            'mapping_type' => self::HAS_ONE,
            'foreign_key'  => 'goods_id',
//            'class_name'=>'GoodsIntro',
        ),
        'GoodsGallery' => array(
            'mapping_type' => self::HAS_MANY,
            'foreign_key'  => 'goods_id',
        ),
    );

    /**
     * @param int|array $id
     * @param int $status
     */
    public function changeStatus($id, $status = -1) {
        //如果id不是数组，就转换成数组，以便后面使用统一的tp的where数组格式。
        if (!is_array($id)) {
            $id = array($id);
        }
        //1.如果status是-1，那么就将原来的名字后添加_del
        if ($status == -1) {
            $data['name'] = array('exp', 'concat(name,"_del")');
        }
        $data['status'] = $status;
        //2.执行数据的更新操作。
        return D('Brand')->where(array('id' => array('in', $id)))->setField($data);
    }

    /**
     * 由于sn有id参与，所以我们需要先插入数据，然后再更新
     * 使用事务，如果都成功，才真正创建了商品
     * 否则，执行回滚操作。
     */
    public function add() {
        $this->startTrans();
        unset($this->data['id']);
        //取得新增记录的主键值
        if (($id = parent::add()) === false) {
            $this->rollback();
            $this->error = '商品添加失败';
            return false;
        }
        //计算货号
        $sn = date('Ymd') . str_pad($id, 9, '0', STR_PAD_LEFT);
        $this->where(array('id' => $id));
        if (parent::save(array('sn' => $sn)) === false) {
            $this->rollback();
            $this->error = '货号保存失败';
            return false;
        }

        //将详细信息保存到goods_intro表中
        $intro = array(
            'goods_id' => $id,
            'content'  => I('post.content', '', false),
        );

        if (D('GoodsIntro')->add($intro) === false) {
            $this->rollback();
            $this->error = '商品详情保存失败';
            return false;
        }

        //将商品的相册图片保存到数据表中
        $gallery = array();
        foreach (I('post.path', '', false) as $path) {
            $gallery[] = array(
                'goods_id' => $id,
                'path'     => $path,
            );
        }
        if (!empty($gallery)) {
            if (M('GoodsGallery')->addAll($gallery) === false) {
                $this->rollback();
                $this->error = '商品相册保存失败';
                return false;
            }
        }

        //将关联文章保存到数据表中
        $articles = array();
        foreach (I('post.article_ids', '', false) as $path) {
            $articles[] = array(
                'goods_id'   => $id,
                'article_id' => $path,
            );
        }
        if (!empty($articles)) {
            $article_model = M('GoodsArticle');
            if ($article_model->addAll($articles) === false) {
                $this->rollback();
                $this->error = '关联文章保存失败';
                return false;
            }
        }
        $this->commit();
    }

    /**
     * 完成商品的更新。
     * 由于需要修改多张表的数据，所以我们重写了save方法，完成了复杂逻辑。
     * @return boolean
     */
    public function save() {
        $request_data = $this->data;
        if (parent::save() === false) {
            $this->error = '商品保存失败';
            return false;
        }
        //商品详细描述的信息
        $data = array(
            'content'  => I('post.content', '', false),
            'goods_id' => $request_data['id'],
        );
        if (D('GoodsIntro')->save($data) === false) {
            $this->error = '商品详情保存失败';
            return false;
        }

        //将商品的相册图片保存到数据表中
        $gallery = array();
        foreach (I('post.path', '', false) as $path) {
            $gallery[] = array(
                'goods_id' => $request_data['id'],
                'path'     => $path,
            );
        }
        if (!empty($gallery)) {
            if (M('GoodsGallery')->addAll($gallery) === false) {
                $this->error = '商品相册保存失败';
                return false;
            }
        }

        //将关联文章保存到数据表中
        $articles = array();
        foreach (I('post.article_ids', '', false) as $path) {
            $articles[] = array(
                'goods_id'   => $request_data['id'],
                'article_id' => $path,
            );
        }
        $article_model = M('GoodsArticle');
        $article_model->where(array('goods_id' => $request_data['id']))->delete();
        if (!empty($articles)) {
            if ($article_model->addAll($articles) === false) {
                $this->error = '关联文章保存失败';
                return false;
            }
        }
        return true;
    }

    public function getList($where, $size) {
        //获取所有的品牌
        $brands    = D('Brand')->field('id,name')->where(array('status' => array('egt', 0)))->select();
        $suppliers = D('Supplier')->field('id,name')->where(array('status' => array('egt', 0)))->select();
        $brands    = get_data_by_column($brands, 'id');
        $suppliers = get_data_by_column($suppliers, 'id');
        $count     = $this->where($where)->count();
        $rows      = $this->where($where)->page(I('get.p', 1), $size)->select();
        foreach ($rows as $key => $value) {
            $rows[$key]['is_best']       = $value['goods_status'] & 1 ? 1 : 0;
            $rows[$key]['is_new']        = $value['goods_status'] & 2 ? 1 : 0;
            $rows[$key]['is_hot']        = $value['goods_status'] & 4 ? 1 : 0;
            $rows[$key]['brand_name']    = $brands[$value['brand_id']]['name'];
            $rows[$key]['supplier_name'] = $suppliers[$value['supplier_id']]['name'];
        }
        return array($count, $rows);
    }

}
