<?php
namespace Admin\Model;

/**
 * 供货商模型。
 *
 * @author kunx
 */
class BrandModel extends \Think\Model{
    protected $patchValidate = true;//开启批量验证
    protected $_validate = array(
        array('name','require','品牌名称不能为空',self::MUST_VALIDATE),
        array('status',array(0,1,-1),'品牌状态不合法',self::MUST_VALIDATE,'in'),
        array('name','','品牌名字不能重复',self::EXISTS_VALIDATE,'unique'),
    );
    
    /**
     * 在tp中如果是一个字段的值是一个集合，应该使用in，in的写法是'字段'=>array('in',$items)
     * @param type $id
     * @param type $status
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
     * 获取所有的分类
     * @return type
     */
    public function getAll(){
        return $this->where(array('status'=>array('egt',0)))->select();
    }
}
