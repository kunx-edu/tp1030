<?php

namespace Admin\Controller;

/**
 * 品牌控制器。
 * @author kunx
 */
class BrandController extends \Think\Controller {

    /**
     * 此方法是基础控制器中定义好的初始化方法，可以做一些具体操作之前的初始化工作。
     * 我们在这里进行标题的统一配置和数据传输。
     */
    public function _initialize() {
        trace(ACTION_NAME);
        $meta_titles = array(
            'index' => '品牌管理',
            'add'   => '添加品牌',
            'edit'  => '修改品牌',
        );
        $meta_title  = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '品牌管理';
        $this->assign('meta_title', $meta_title);
    }

    public function index($keyword = '') {
        //1.判断是否有搜索
        //2.如果有搜索，就拼凑关键字条件
//        $keyword = I('get.keyword','');
        if ($keyword) {
            $where['name'] = array('like', $keyword . '%');
        }
        $where['status'] = array('egt', 0);

        $model = D('Brand');
        $count = $model->where($where)->count(); //获取总行数
        //3.分页
        $size  = C('PAGE_SIZE') ? C('PAGE_SIZE') : 10;
        if ($count > $size) {
            $page      = new \Think\Page($count, $size);
            //4.获取分页代码
            $page->setConfig('theme', C('PAGE_THEME')); //获取统一翻页样式
            $page_html = $page->show(); //获取分页代码
        } else {
            $page_html = '';
        }
        $rows = $model->where($where)->page(I('get.p', 1), $size)->select(); //获取结果集
        //5.传递数据
        $this->assign('page_html', $page_html);
        $this->assign('rows', $rows);
        $this->assign('keyword', $keyword);
        $this->display();
    }

    public function add() {
        //1.如果有提交
        if (IS_POST) {
            //1.1创建模型
            $model = D('Brand');
            //1.2验证
            if ($model->create()) {
                if ($model->add()) {
                    //1.3添加数据
                    $this->success('添加成功', U('index'));
                } else {
                    $this->error(get_errors($model->getError()));
                }
            } else {
                $this->error('数据不合法');
            }
            //1.4跳转
        } else {
            //2.如果没有提交
            $this->display('edit');
        }
    }

    public function edit($id) {
        $model = D('Brand');
        //1.判断是否有提交
        if(IS_POST){
            //1.1检查数据是否合法
            if($model->create()){
                if($model->save()){
                    $this->success('修改成功',U('index'));
                }else{
                    $this->error(get_errors($model->getError()));
                }
            }else{
                $this->error('数据不合法');
            }
        }else{
            //2.如果没有提交，直接展示数据
            $row = $model->find($id);
            $this->assign('row',$row);
            $this->display();
        }
    }

    public function delete() {
        $this->display();
    }

}
