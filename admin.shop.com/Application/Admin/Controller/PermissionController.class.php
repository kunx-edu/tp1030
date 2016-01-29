<?php

namespace Admin\Controller;

/**
 * @author kunx
 */
class PermissionController extends \Think\Controller {

    /**
     * 此方法是基础控制器中定义好的初始化方法，可以做一些具体操作之前的初始化工作。
     * 我们在这里进行标题的统一配置和数据传输。
     */
    public function _initialize() {
        trace(ACTION_NAME);
        $meta_titles = array(
            'index' => '权限管理',
            'add'   => '添加权限',
            'edit'  => '修改权限',
        );
        $meta_title  = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '权限管理';
        $this->assign('meta_title', $meta_title);
    }

    /**
     * 权限列表.
     * 具备分页功能.
     */
    public function index() {
        //记录当前页面地址，为编辑跳转做准备
        cookie('forward', __SELF__);
        $model = D('Permission');
        //2.获取数据
        //3.展示数据
        $this->assign('rows',$model->getList());

        $this->display();
    }

    /**
     * 添加权限
     */
    public function add() {
        $model = D('Permission');
        //1.判断是否是post提交
        if (IS_POST) {
            //1.1判断数据是否合法create
            if ($model->create()) {
                //1.2插入数据
                if ($model->addPermission() !== false) {
                    $this->success('插入成功', U('index'));
                } else {
                    $this->error('插入失败');
                }
            } else {
                $this->error(get_errors($model->getError()));
            }
        } else {
            //2.如果不是就展示
            $rows = $model->getList(array(),true);
            $this->assign('rows',$rows);
            $this->display('edit');
        }
    }

    /**
     * 修改权限
     */
    public function edit($id) {
        $model = D('Permission');
        if (IS_POST) {
            if ($model->create()) {
                if ($model->savePermission() !== false) {
                    $this->success('修改成功', cookie('forward'));
                } else {
                    $this->error('修改失败');
                }
            } else {
                $this->error(get_errors($model->getError()));
            }
        } else {
            //1.根据id获取数据表中的数据
            $row = $model->find($id);
            $this->assign('row', $row);
            $rows = $model->getList(array(),true);
            $this->assign('rows',$rows);
            $this->display();
        }
    }

    /**
     * 修改供应商的状态，删除也是修改状态，所以可以都使用
     * @param type $id
     * @param type $status
     */
    public function changeStatus($id, $status = -1) {
        $model = D('Permission');
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
