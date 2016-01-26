<?php 
namespace Admin\Model;

/**
 *
 * @author kunx
 */
class GoodsModel extends \Think\Model{
    protected $patchValidate = true;//开启批量验证
    protected $_validate = array(
        array('name','require','名称不能为空'),
//	array('sn','require','货号不能为空'),
	array('logo','require','商品LOGO不能为空'),
	array('goods_category_id','require','商品分类不能为空'),
	array('brand_id','require','品牌不能为空'),
	array('supplier_id','require','供货商不能为空'),
	array('market_price','require','市场价格不能为空'),
	array('shop_price','require','本店价格不能为空'),
	array('stock','require','库存不能为空'),
//	array('goods_status','require','商品状态不能为空'),
	array('is_on_sale','require','是否上架不能为空'),
	array('status','require','状态@radio|1=是&0=否不能为空'),
	array('sort','require','排序不能为空'),
    );
    
    /**
     * 调用create的时候，自动完成的操作
     * @var type 
     */
    protected $_auto = array(
        array('goods_status','array_sum',3,'function'),//对于goods_status字段进行求并集运算
        array('inputtime',NOW_TIME),//插入的时候自动设定当前时间
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
}
