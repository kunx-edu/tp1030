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
}
