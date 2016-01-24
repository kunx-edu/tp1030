<?php

namespace Admin\Model;

/**
 *
 * @author kunx
 */
class GoodsCategoryModel extends \Think\Model {

    protected $patchValidate = true; //开启批量验证
    protected $_validate     = array(
        array('name', 'require', '名称不能为空'),
        array('parent_id', 'require', '父分类不能为空'),
        array('lft', 'require', '左边界不能为空'),
        array('rght', 'require', '右边界不能为空'),
        array('level', 'require', '级别不能为空'),
        array('status', 'require', '状态@radio@1=是&0=否不能为空'),
        array('sort', 'require', '排序不能为空')
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
     * 重写基础模型类中的add方法，调用了nestedsets的机制。
     * @return boolean
     */
    public function add() {
        $data           = $this->data; //获取用户提交的数据
        //1实例化需要执行计算的NestedSets
        //1.1创建一个用于执行数据库命令的对象
        $db_mysql_model = D('DbMysql');
        $nested_sets    = new \Admin\Service\NestedSets($db_mysql_model, 'goods_category', 'lft', 'rght', 'parent_id', 'id', 'level');
        if ($nested_sets->insert($data['parent_id'], $data, 'bottom') === false) {
            $this->error = '创建分类失败';
            return false;
        } else {
            return true;
        }
    }

    /**
     * 保存分类的修改。
     * 如果父级分类没有变化，使用nestedSets一定会返回false
     * 所以我们要区别对待，修改父级分类和不修改进行判断。
     * @return boolean
     */
    public function save() {
        /**
         * 此处不要使用find查询原来的数据，原因是，find会对当前对象的data属性进行修改，修改为数据库中的内容
         * 从而导致$this->create()收集到的数据被污染
         */
        $parent_id=$this->getFieldById($this->data['id'],'parent_id');
        if($parent_id != $this->data['parent_id']){
            //>>1.执行sql的对象
           $db_mysql_model  = new \Admin\Model\DbMysqlModel();
           //>>2.完成业务运算的对象
           $nested_sets = new \Admin\Service\NestedSets($db_mysql_model,'goods_category','lft','rght','parent_id','id','level');
            $result = $nested_sets->moveUnder($this->data['id'], $this->data['parent_id'],'bottom');
            if($result===false){
                $this->error = '移动失败!不能够移动到自己的子节点下!';
                return false;
            }
        }
        //>>4.更新其他表单字段中的数据
        return parent::save();
    }

    /**
     * 物理删除，会重拍左右节点及层级。
     * @param type $id
     */
    public function delete($id) {
        $db_mysql_model = D('DbMysql');
        $nested_sets    = new \Admin\Service\NestedSets($db_mysql_model, 'goods_category', 'lft', 'rght', 'parent_id', 'id', 'level');
        $nested_sets->delete($id);
    }
    
    /**
     * 逻辑删除分类
     * 在name后面拼接_del并且将状态改为-1
     * 由于前面重写了save方法,所以保存的时候应当调用父类的save方法
     * @param int $id
     * @return int|false
     */
    public function delete2($id){
        //1.获取原始数据
        $this->field('lft,rght')->where(array('id'=>$id))->find();
        //准备修改的数据
        $data = array(
            'status'=>-1,
            'name'=>array('exp','concat(name,"_del")'),
        );
        //update goods_category set status=-1,name=concat(name,'_del') where lft>=2 and rght<=9
        //准备用于修改的条件
        $cond = array(
            'lft'=>array('egt',$this->data['lft']),
            'rght'=>array('elt',$this->data['rght']),
        );
        $this->where($cond);
        return parent::save($data);
    }
    
    /**
     * 获取所有的没有被删除的分类
     * 返回json字符串，便于js使用
     */
    public function getList(){
        return json_encode($this->where(array('status'=>array('egt',0)))->order('lft')->select());
    }
}
