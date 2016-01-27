<?php 
namespace Admin\Controller;

/**
 * @author kunx
 */
class ArticleCategoryController extends \Think\Controller {

    /**
     * 此方法是基础控制器中定义好的初始化方法，可以做一些具体操作之前的初始化工作。
     * 我们在这里进行标题的统一配置和数据传输。
     */
    public function _initialize() {
        trace(ACTION_NAME);
        $meta_titles = array(
            'index' => '文章分类管理',
            'add'   => '添加文章分类',
            'edit'  => '修改文章分类',
        );
        $meta_title  = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '文章分类管理';
        $this->assign('meta_title', $meta_title);
    }

    /**
     * 文章分类列表.
     * 具备分页功能.
     */
    public function index($keyword = '') {
        //记录当前页面地址，为编辑跳转做准备
        cookie('forward',__SELF__);
        
        //1.创建模型
        $model           = D('ArticleCategory');
        //2.获取数据
        $where['status'] = array('egt', 0);
        if ($keyword) {
            $where['name'] = array('like', $keyword . '%');
        }
        //5.获取满足条件的总行数
        $count = $model->where($where)->count();
        //5.2获取分页html代码
        $size = C('PAGE_SIZE')?C('PAGE_SIZE'):10;
        
        $rows = $model->where($where)->page(I('get.p',1),$size)->select();
        //3.展示数据
        $this->assign('rows', $rows);
        $this->display();
    }

    /**
     * 添加文章分类
     */
    public function add() {
        //1.判断是否是post提交
        if (IS_POST) {
            $model = D('ArticleCategory');
            //1.1判断数据是否合法create
            if ($model->create()) {
                //1.2插入数据
                if ($model->add() !== false) {
                    $this->success('插入成功', U('index'));
                } else {
                    $this->error('插入失败');
                }
            } else {
                $this->error(get_errors($model->getError()));
            }
        } else {
            //2.如果不是就展示
            $this->display('edit');
        }
    }

    /**
     * 修改文章分类
     */
    public function edit($id) {
        $model = D('ArticleCategory');
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
        $model = D('ArticleCategory');
        $data = array('status'=> $status);
        if($status==-1){
            $data['name']=array('exp','concat(name,"_del")');
        }
        $flag  = $model->where(array('id' => $id))->setField($data);
        if ($flag !== false) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }

}
