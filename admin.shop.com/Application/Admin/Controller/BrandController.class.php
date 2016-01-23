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
        //6.记录页面地址，便于后续的编辑、修改、删除的跳转
        cookie('forward',__SELF__);
        //1.判断是否有搜索
        //2.如果有搜索，就拼凑关键字条件
//        $keyword = I('get.keyword','');
        if ($keyword) {
            $where['name'] = array('like', $keyword . '%');
        }
        $where['status'] = array('egt', 0);
        $p = I('get.p', 1);

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
        $rows = $model->where($where)->page($p, $size)->select(); //获取结果集
        //5.传递数据
        $this->assign('page_html', $page_html);
        $this->assign('rows', $rows);
        $this->assign('keyword', $keyword);
        $this->assign('page', $p);
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
                    $this->success('修改成功',cookie('forward'));
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

    public function changeStatus($id,$status=-1) {
//        dump($id);
//        exit;
        //1.如果status是-1，那么就将原来的名字后添加_del
//        if($status==-1){
//            $data['name'] = array('exp','concat(name,"_del")');
//        }
//        $data['status']=$status;
//        //2.执行数据的更新操作。
//        if(D('Brand')->where(array('id'=>array('in',$id)))->setField($data) !== false){
//            $this->success('操作成功',  cookie('forward'));
//        }else{
//            $this->error('操作失败');
//        }
        $model = D('Brand');
        if($model->changeStatus($id,$status)!==false){
            $this->success('操作成功',  cookie('forward'));
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 批量添加品牌。
     * 表单中一行就是一个记录，字段之间使用 制表符 分割
     */
    public function patchAdd(){
        if(IS_POST){
            $content = I('post.content');
            $rows = explode("\n", $content);
            $data = array();
            foreach($rows as $row){
                $tmp = explode("\t", $row);
                if(!trim($tmp[0])){
                    continue;
                }
                $data[] = array(
                    'name'=>$tmp[0],
                    'intro'=>$tmp[1],
                    'sort'=>$tmp[2],
                    'status'=>$tmp[3],
                );
            }
            $model = D('Brand');
            if($model->addAll($data)){
                $this->success('成功',U('index'));
            }else{
                $this->error('失败');
            }
        }else{
            $this->display('patch_add');
        }
    }
    
    
    public function getListByAjax($keyword = ''){
        if ($keyword) {
            $where['name'] = array('like', $keyword . '%');
        }
        $where['status'] = array('egt', 0);

        $model = D('Brand');
        $count = $model->where($where)->count(); //获取总行数
        //3.分页
        $size  = C('PAGE_SIZE') ? C('PAGE_SIZE') : 10;
        if ($count > $size) {
//            $page      = new \Think\Page($count, $size);
            
            //http://admin.shop.com/index.php/Brand/getListByAjax.html?keyword=&p=1
            $page      = new \Admin\Logic\Page($count, $size);
            //4.获取分页代码
            $page->setConfig('theme', C('PAGE_THEME')); //获取统一翻页样式
            $page_html = $page->show('index'); //获取分页代码
        } else {
            $page_html = '';
        }
        $rows = $model->where($where)->page(I('get.p', 1), $size)->select(); //获取结果集
        //data用于交给ajax，其中的data元素是品牌列表的具体数据
        $data = array();
        foreach($rows as $row){
            $data[] = array_merge($row,array(
                'edit_url'=> U('edit',array('id'=>$row['id'])),
                'change_status_url'=>U('changeStatus',array('id'=>$row['id'])),
            ));
        }
        $data = array(
            'data'=>$data,
            'page_html'=>$page_html,
        );
        echo json_encode($data);
        exit;
    }
}
