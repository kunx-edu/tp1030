<?php 
namespace Admin\Controller;

/**
 * @author kunx
 */
class GoodsCategoryController extends \Think\Controller {

    /**
     * 此方法是基础控制器中定义好的初始化方法，可以做一些具体操作之前的初始化工作。
     * 我们在这里进行标题的统一配置和数据传输。
     */
    public function _initialize() {
        trace(ACTION_NAME);
        $meta_titles = array(
            'index' => '商品分类管理',
            'add'   => '添加商品分类',
            'edit'  => '修改商品分类',
        );
        $meta_title  = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '商品分类管理';
        $this->assign('meta_title', $meta_title);
    }

    /**
     * 商品分类列表.
     * 具备分页功能.
     */
    public function index() {
        //记录当前页面地址，为编辑跳转做准备
        cookie('forward',__SELF__);
        
        //1.创建模型
        $model           = D('GoodsCategory');
        //2.获取数据
        $where['status'] = array('egt', 0);
//        $rows = $model->where($where)->page(I('get.p',1),$size)->select();
        $rows = $model->where($where)->order('lft')->select();
        //3.展示数据
        $this->assign('rows', $rows);
//        $this->assign('keyword', $keyword);
//        $this->assign('page_html', $page_html);
        $this->display();
    }

    /**
     * 添加商品分类
     * 
     * 当用户新增分类的时候，提交了基本信息和父级分类id
     * 然后，nestedsets自动帮我们计算左右节点和层级
     * 接着调用执行sql语句的代码，进行执行并返回
     */
    public function add() {
        $model = D('GoodsCategory');
        //获取所有的分类信息
        //1.判断是否是post提交
        if (IS_POST) {
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
            //将所有的分类传给前端模板
            $rows = $model->getList();
            $this->assign('rows',  $rows);
            //2.如果不是就展示
            $this->display('edit');
        }
    }

    /**
     * 修改商品分类
     */
    public function edit($id) {
        $model = D('GoodsCategory');
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
            $rows = $model->getList();
            $row = $model->find($id);
            $this->assign('row', $row);
            $this->assign('rows',  $rows);
            $this->display();
        }
    }
    
    /**
     * 逻辑删除。
     * @param type $id
     * @param type $status
     */
    public function delete($id,$status=-1){
        $model = D('GoodsCategory');
        if($model->delete2($id)!==false){
            $this->success('删除成功',U('index'));
        }else{
            $this->error('删除失败');
        }
    }

    /**
     * 修改供应商的状态，删除也是修改状态，所以可以都使用
     * @param type $id
     * @param type $status
     */
    public function changeStatus($id, $status = -1) {
        $model = D('GoodsCategory');
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
