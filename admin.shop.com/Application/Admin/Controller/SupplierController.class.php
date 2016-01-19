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
class SupplierController extends \Think\Controller{
    
    /**
     * 此方法是基础控制器中定义好的初始化方法，可以做一些具体操作之前的初始化工作。
     * 我们在这里进行标题的统一配置和数据传输。
     */
    public function _initialize(){
        trace(ACTION_NAME);
        $meta_titles = array(
            'index'=>'供货商管理',
            'add'=>'添加供货商',
            'edit'=>'修改供货商',
        );
        $meta_title = isset($meta_titles[ACTION_NAME])?$meta_titles[ACTION_NAME]:'供货商管理';
        $this->assign('meta_title',$meta_title);
    }

    /**
     * 供货商列表.
     */
    public function index() {
        
        //1.创建模型
        $model = D('Supplier');
        //2.获取数据
        $where['status']=array('egt',0);
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
        $this->assign('rows',$rows);
//        $this->assign('meta_title','供货商管理');
        $this->display();
    }

    /**
     * 添加供货商
     */
    public function add() {
        
    }

    /**
     * 修改供货商
     */
    public function edit() {
        
    }

    /**
     * 删除供货商
     */
    public function delete() {
        
    }

}
