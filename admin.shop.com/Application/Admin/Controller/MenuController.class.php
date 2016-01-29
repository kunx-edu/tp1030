<?php

namespace Admin\Controller;

/**
 * @author kunx
 */
class MenuController extends \Think\Controller {

    /**
     * 此方法是基础控制器中定义好的初始化方法，可以做一些具体操作之前的初始化工作。
     * 我们在这里进行标题的统一配置和数据传输。
     */
    public function _initialize() {
        trace(ACTION_NAME);
        $meta_titles = array(
            'index' => '菜单管理',
            'add'   => '添加菜单',
            'edit'  => '修改菜单',
        );
        $meta_title  = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '菜单管理';
        $this->assign('meta_title', $meta_title);
    }

    /**
     * 菜单列表.
     * 具备分页功能.
     */
    public function index($keyword = '') {
        //记录当前页面地址，为编辑跳转做准备
        cookie('forward', __SELF__);

        //1.创建模型
        $model = D('Menu');
        $rows  = $model->getList();
        //3.展示数据
        $this->assign('rows', $rows);
        $this->display();
    }

    /**
     * 添加菜单
     */
    public function add() {
        //1.判断是否是post提交
        if (IS_POST) {
            $model = D('Menu');
            //1.1判断数据是否合法create
            if ($model->create()) {
                //1.2插入数据
                if ($model->addMenu() !== false) {
                    $this->success('插入成功', U('index'));
                } else {
                    $this->error(get_errors($model->getError()));
                }
            } else {
                $this->error(get_errors($model->getError()));
            }
        } else {
            //2.如果不是就展示
            //取出所有的权限
            $this->assign('all_permissions', D('Permission')->getList('id,name,parent_id', array(), true));
            $this->display('edit');
        }
    }

    /**
     * 修改菜单
     */
    public function edit($id) {
        $model = D('Menu');
        if (IS_POST) {
            if ($model->create()) {
                if ($model->save() !== false) {
                    $this->success('修改成功', cookie('forward'));
                } else {
                    $this->error('修改失败');
                }
            } else {
                $this->error(get_errors($model->getError()));
            }
        } else {
            //1.根据id获取数据表中的数据
            //获取所有的菜单
            $this->assign('all_menu',$model->getList(array('id,name,parent_id'),array(),true));
            //获取所有的权限
            //取出所有的权限
            $this->assign('all_permissions',D('Permission')->getList('id,name,parent_id',array(),true));
            //获取当前关联的权限
            $this->assign('perms',$model->getPermission($id,true));
            $row = $model->find($id);
            $this->assign('row', $row);
            $this->display();
        }
    }

    /**
     * 修改供应商的状态，删除也是修改状态，所以可以都使用
     * @param type $id
     * @param type $status
     */
    public function changeStatus($id, $status = -1) {
        $model = D('Menu');
        $data  = array('status' => $status);
        if ($status == -1) {
            $data['name'] = array('exp', 'concat(name,"_del")');
        }
        $flag = $model->where(array('id' => $id))->setField($data);
        if ($flag !== false) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }

}
