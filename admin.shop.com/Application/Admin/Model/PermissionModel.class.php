<?php

namespace Admin\Model;

/**
 *
 * @author kunx
 */
class PermissionModel extends \Think\Model {

    protected $patchValidate = true; //开启批量验证
    protected $_validate     = array(
//        array('name','require','名称不能为空'),
//	array('path','require','URL不能为空'),
//	array('parent_id','require','父分类不能为空'),
//	array('lft','require','左边界不能为空'),
//	array('rght','require','右边界不能为空'),
//	array('level','require','级别不能为空'),
//	array('status','require','状态@radio|1=是&0=否不能为空'),
//	array('sort','require','排序不能为空')
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
     * 添加权限
     * @return boolean
     */
    public function addPermission() {
        $db          = D('DbMysql');
        $nested_sets = new \Admin\Service\NestedSets($db, 'permission', 'lft', 'rght', 'parent_id', 'id', 'level');
        if ($nested_sets->insert($this->data['parent_id'], $this->data, 'bottom') !== false) {
            return true;
        } else {
            $this->error = '添加失败';
            return false;
        }
    }

    /**
     * 修改权限，如果不修改父级分类，不执行nestedSets的操作
     */
    public function savePermission() {
        //先获取到使用create处理过的数据，使用传值赋值
        $data = $this->data;
        //获取数据表中的数据
        $row  = $this->find($this->data['id']);
        //如果当前父级分类和修改后的父级分类不一致，才需要计算左右节点
        if ($data['parent_id'] != $row['parent_id']) {
            $db          = D('DbMysql');
            $nested_sets = new \Admin\Service\NestedSets($db, 'permission', 'lft', 'rght', 'parent_id', 'id', 'level');
            $nested_sets->moveUnder($this->data['id'], $data['parent_id'], 'bottom');
        }
        $this->save($data); //保存用户提交的数据，由于用户没有提交节点和层级，所以不会导致数据被破坏
    }

    public function getList($where = array(), $is_ajax) {
        $where['status'] = array('gt', 0);
        //5.获取满足条件的总行数
        $count           = $this->where($where)->count();

        $rows = $this->order('lft')->where($where)->select();
        if ($is_ajax) {
            return json_encode($rows);
        } else {
            return $rows;
        }
    }

}
