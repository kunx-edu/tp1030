<?php 
namespace Admin\Model;

/**
 *
 * @author kunx
 */
class RoleModel extends \Think\Model{
    protected $patchValidate = true;//开启批量验证
    protected $_validate = array(
        array('name','require','名称不能为空'),
	array('status','require','状态@radio|1=是&0=否不能为空'),
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
    
    
    public function addRole(){
        $this->startTrans();
        //1.获取数据，先保存自身角色表
        if(($id = $this->add())===false){
            $this->error = '角色创建失败';
            $this->rollback();
            return false;
        }
        $permissions = I('post.permission_ids');
        if($this->permissionHandler($id, $permissions) === false){
            $this->error = '角色权限关联失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }
    
    private function permissionHandler($role_id,array $permissions){
        //2.将角色和权限的关系存放到角色-权限中间表
        $data = array();
        foreach ($permissions as $pid){
            $data[] = array(
                'role_id'=>$role_id,
                'permission_id'=>$pid,
            );
        }
        //执行保存
        if(M('RolePermission')->addAll($data) === false){
            return false;
        }
        return true;
    }


    /**
     * 获取指定角色所拥有的权限列表。
     * @param integer $role_id
     * @param boolean $is_ajax
     * @return type
     */
    public function getPermission($role_id,$is_ajax=false){
        $rows = M('RolePermission')->where(array('role_id'=>$role_id))->select();
        if($is_ajax){
            return json_encode($rows);
        }else{
            return $rows;
        }
    }
    
    public function saveRole(){
        $data = $this->data;
        $this->startTrans();
        if($this->save() === false){
            $this->error = '角色修改失败';
            $this->rollback();
            return false;
        }
        //执行权限的保存操作
        //先删除当前角色对应的权限，然后再添加
        M('RolePermission')->where(array('role_id'=>$data['id']))->delete();
        $permissions = I('post.permission_ids');
        if($this->permissionHandler($data['id'], $permissions) === false){
            $this->error = '权限保存失败';
            $this->rollback();
            return false;
        }
        $this->commit();
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
}
