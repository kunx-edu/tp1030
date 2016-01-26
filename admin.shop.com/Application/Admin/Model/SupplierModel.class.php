<?php
namespace Admin\Model;

/**
 * 供货商模型。
 *
 * @author kunx
 */
class SupplierModel extends \Think\Model{
    protected $patchValidate = true;//开启批量验证
    protected $_validate = array(
        array('name','require','供应商名称不能为空',self::MUST_VALIDATE),
        array('status',array(0,1,-1),'供应商状态不合法',self::MUST_VALIDATE,'in'),
        array('name','','供应商名字不能重复',self::EXISTS_VALIDATE,'unique'),
    );
    
    
    public function getAll(){
        return $this->where(array('status'=>array('egt',0)))->select();
    }
}
