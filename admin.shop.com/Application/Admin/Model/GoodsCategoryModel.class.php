<?php 
namespace Admin\Model;

/**
 *
 * @author kunx
 */
class GoodsCategoryModel extends \Think\Model{
    protected $patchValidate = true;//开启批量验证
    protected $_validate = array(
        array('name','require','名称不能为空'),
	array('parent_id','require','父分类不能为空'),
	array('lft','require','左边界不能为空'),
	array('rght','require','右边界不能为空'),
	array('level','require','级别不能为空'),
	array('status','require','状态@radio@1=是&0=否不能为空'),
	array('sort','require','排序不能为空')
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
