<?php 
namespace Admin\Model;

/**
 *
 * @author kunx
 */
class MenuModel extends \Think\Model{
    protected $patchValidate = true;//开启批量验证
    protected $_validate = array(
        array('name','require','名称不能为空'),
	array('parent_id','require','父分类不能为空'),
	array('status','require','状态不能为空'),
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
    
    
    public function addMenu(){
        $db          = D('DbMysql');
        $nested_sets = new \Admin\Service\NestedSets($db, 'menu', 'lft', 'rght', 'parent_id', 'id', 'level');
        $id = $nested_sets->insert($this->data['parent_id'], $this->data, 'bottom');
        if ($id === false) {
            $this->error = '菜单创建失败';
            return false;
        }
        //保存菜单和权限的映射关系
        $permissions = I('post.permission_ids');
        if($this->permissionHandler($id, $permissions) === false){
            $this->error = '菜单权限关联失败';
            return false;
        }
        return true;
    }
    
    private function permissionHandler($menu_id,array $permissions){
        //2.将角色和权限的关系存放到角色-权限中间表
        $data = array();
        foreach ($permissions as $pid){
            $data[] = array(
                'menu_id'=>$menu_id,
                'permission_id'=>$pid,
            );
        }
        //执行保存
        if(M('MenuPermission')->addAll($data) === false){
            return false;
        }
        return true;
    }
    
    /**
     * 获取所有的启用的角色
     * @param array $where 条件
     * @param boolean $is_ajax 是否返回json
     * @return string|array
     */
    public function getList($field='*',array $where = array(), $is_ajax=false) {
        $where['status'] = array('gt', 0);
        $rows = $this->field($field)->where($where)->select();
        if ($is_ajax) {
            return json_encode($rows);
        } else {
            return $rows;
        }
    }
    
    /**
     * 获取哪些权限可以看到指定菜单。
     * @param integer $menu_id
     * @param boolean $is_ajax
     * @return type
     */
    public function getPermission($menu_id,$is_ajax=false){
        $rows = M('MenuPermission')->where(array('menu_id'=>$menu_id))->select();
        if($is_ajax){
            return json_encode($rows);
        }else{
            return $rows;
        }
    }
}
