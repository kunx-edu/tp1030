<?php

/**
 * 此文件是供货商控制器。
 * 完成供货商功能的操作。
 */

namespace Admin\Controller;

/**
 * 供货商控制器。
 * @author kunx
 */
class SupplierController extends \Think\Controller {

    /**
     * 此方法是基础控制器中定义好的初始化方法，可以做一些具体操作之前的初始化工作。
     * 我们在这里进行标题的统一配置和数据传输。
     */
    public function _initialize() {
        trace(ACTION_NAME);
        $meta_titles = array(
            'index' => '供货商管理',
            'add'   => '添加供货商',
            'edit'  => '修改供货商',
        );
        $meta_title  = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '供货商管理';
        $this->assign('meta_title', $meta_title);
    }

    /**
     * 供货商列表.
     */
    public function index($keyword = '') {

        //1.创建模型
        $model           = D('Supplier');
        //2.获取数据
        $where['status'] = array('egt', 0);
        if ($keyword) {
            $where['name'] = array('like', $keyword . '%');
        }
        $rows = $model->where($where)->select();
        /*
          //4.为了展示对错图标，我们需要处理status字段
          foreach($rows as $key=>$row){
          if($row['status']){
          $rows[$key]['enable']='yes.gif';
          }else{
          $rows[$key]['enable']='no.gif';
          }
          }
         */
        //3.展示数据
        $this->assign('rows', $rows);
        $this->assign('keyword', $keyword);
//        $this->assign('meta_title','供货商管理');
        $this->display();
    }

    /**
     * 添加供货商
     */
    public function add() {
        //1.判断是否是post提交
        if (IS_POST) {
            $model = D('Supplier');
            //1.1判断数据是否合法create
            if ($model->create()) {
                //1.2插入数据
                if ($model->add() !== false) {
                    $this->success('插入成功', U('index'));
                } else {
                    $this->error('插入失败');
                }
            } else {
//                $errors = '<ul>';
//                foreach ($model->getError() as $error) {
//                    $errors .= '<li>' . $error . '</li>';
//                }
//                $errors .= '</ul>';
//                $this->error($errors);
                $this->error(get_errors($model->getError()));
            }
        } else {
            //2.如果不是就展示
            $this->display('edit');
        }
    }

    /**
     * 修改供货商
     */
    public function edit($id) {
        $model = D('Supplier');
        if (IS_POST) {
            if ($model->create()) {
                if ($model->save() !== false) {
                    $this->success('修改成功', U('index'));
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
            $this->display();
        }
    }

    /**
     * 修改供应商的状态，删除也是修改状态，所以可以都使用
     * @param type $id
     * @param type $status
     */
    public function changeStatus($id, $status = -1) {
        $model = D('Supplier');
        $flag  = $model->where(array('id' => $id))->setField('status', $status);
        if ($flag) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }

}
