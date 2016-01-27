<?php

namespace Admin\Controller;

/**
 * @author kunx
 */
class GoodsController extends \Think\Controller {

    /**
     * 此方法是基础控制器中定义好的初始化方法，可以做一些具体操作之前的初始化工作。
     * 我们在这里进行标题的统一配置和数据传输。
     */
    public function _initialize() {
        trace(ACTION_NAME);
        $meta_titles = array(
            'index' => '商品管理',
            'add'   => '添加商品',
            'edit'  => '修改商品',
        );
        $meta_title  = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '商品管理';
        $this->assign('meta_title', $meta_title);
    }

    /**
     * 商品列表.
     * 具备分页功能.
     */
    public function index() {
        //记录当前页面地址，为编辑跳转做准备
        cookie('forward', __SELF__);
        /**
         * 获取并拼接查询条件
         */
        $conditions['status'] = array('egt', 0);
        //是否带有分类要求
        $search_category = I('get.search_category');
        if ($search_category && $search_category > 0) {
            //先查询所有的后代分类id列表
            $children_category = D('GoodsCategory')->getChildren($search_category); 
            $tmp_category = array_merge(array($search_category),$children_category);
            $conditions['goods_category_id'] = array('in',$tmp_category);
            unset($tmp_category);
        }
        
        //是否带有品牌条件
        $search_brand = I('get.search_brand');
        if ($search_brand && $search_brand > 0) {
            $conditions['brand_id'] = $search_brand;
        }
        
        //是否带有状态条件
        $search_status = I('get.search_status');
        if ($search_status > 0) {
            $conditions['_string'] = 'goods_status & '. $search_status;
        }
        
        //是否带有状态条件
        $search_is_on_sale = I('get.search_is_on_sale',-1);
        if ($search_is_on_sale == '-1') {
            
        } else{
            $conditions['is_on_sale'] = $search_is_on_sale;
        }
        
        $search_keyword = I('get.search_keyword');
        if ($search_keyword) {
            $conditions['name'] = array('like', $search_keyword . '%');
        }
        //1.创建模型
        $model           = D('Goods');
        
        //5.获取满足条件的总行数
        $size      = C('PAGE_SIZE') ? C('PAGE_SIZE') : 10;
        list($count, $rows) = $model->getList($conditions, $size);
        //5.2获取分页html代码
        $page      = new \Think\Page($count, $size);
        $page->setConfig('theme', C('PAGE_THEME'));
        $page_html = $page->show();
        //3.展示数据
        $this->assign('rows', $rows);
        $this->assign('page_html', $page_html);
        $this->assign('categorys', D('GoodsCategory')->getList('id,name', false));
        $this->assign('brands', D('Brand')->getAll('id,name'));
        $this->assign('statuses', \Admin\Model\GoodsModel::$statuses);
        $this->assign('is_on_sale', \Admin\Model\GoodsModel::$isOnSale);
        $search = array(
            'category'=>$search_category,
            'brand'=>$search_brand,
            'status'=>$search_status,
            'is_on_sale'=>$search_is_on_sale,
            'keyword'=>$search_keyword,
            );
        $this->assign('search', $search);
        $this->display();
    }

    /**
     * 添加商品
     */
    public function add() {
        //1.判断是否是post提交
        if (IS_POST) {
            $model = D('Goods');
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
            $brands    = D('Brand')->getAll();
            $suppliers = D('Supplier')->getAll();
            $categorys = D('GoodsCategory')->getList();
            $this->assign('categorys', $categorys);
            $this->assign('brands', $brands);
            $this->assign('suppliers', $suppliers);
            $this->display('edit');
        }
    }

    /**
     * 修改商品
     */
    public function edit($id) {
        $model = D('Goods');
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
            $row = $model->relation(array('GoodsIntro', 'GoodsGallery'))->find($id);
            $this->assign('row', $row);
            $this->assign('categorys', D('GoodsCategory')->getList());
            $this->assign('brands', D('Brand')->getAll());
            $this->assign('suppliers', D('Supplier')->getAll());
            $this->assign('articles', D('Article')->getArticles($id));
            $this->display();
        }
    }

    /**
     * 修改供应商的状态，删除也是修改状态，所以可以都使用
     * @param type $id
     * @param type $status
     */
    public function changeStatus($id, $status = -1) {
        $model = M('Goods');
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
