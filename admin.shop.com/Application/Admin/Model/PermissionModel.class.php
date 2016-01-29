<?php

namespace Admin\Model;

/**
 *
 * @author kunx
 */
class PermissionModel extends \Think\Model {

    protected $patchValidate = true; //开启批量验证
    protected $_validate     = array(
        array('name', 'require', '名称不能为空'),
        array('path', 'require', 'URL不能为空'),
        array('parent_id', 'require', '父权限不能为空'),
        array('status', 'require', '状态不能为空'),
        array('sort', 'require', '排序不能为空')
    );

    /**
     * 设定状态,如果是删除，把后代权限也删除。
     * @param int|array $id
     * @param int $status
     */
    public function changeStatus($id, $status = 0) {
        $row            = $this->field('lft,rght')->find($id);
        //如果id不是数组，就转换成数组，以便后面使用统一的tp的where数组格式。
        //1.如果status是-1，那么就将原来的名字后添加_del
        $data['status'] = $status;
        //2.执行数据的更新操作。
        if ($status == 0) {
            $cond = array(
                'lft'  => array('egt', $row['lft']),
                'rght' => array('elt', $row['rght'])
            );
        } else {
            $cond = array('id' => $id);
        }
        return $this->where($cond)->setField($data);
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
        $data['name'] .= '_del';
        $this->save($data); //保存用户提交的数据，由于用户没有提交节点和层级，所以不会导致数据被破坏
    }

    /**
     * 获取所有的启用的权限
     * @param array $where 条件
     * @param boolean $is_ajax 是否返回json
     * @return string|array
     */
    public function getList($field = '*', array $where = array(), $is_ajax = false) {
        //权限删除后仍旧可以看到，所以这里执行条件的合并，如果想看所有的，就在调用的时候传递status数组
        $tmp['status'] = array('gt', 0);
        $where = array_merge($tmp,$where);

        $rows = $this->field($field)->order('lft')->where($where)->select();
        if ($is_ajax) {
            return json_encode($rows);
        } else {
            return $rows;
        }
    }

}
