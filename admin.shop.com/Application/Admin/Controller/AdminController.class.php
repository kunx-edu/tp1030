<?php

namespace Admin\Controller;

/**
 * @author kunx
 */
class AdminController extends \Think\Controller {

    /**
     * 此方法是基础控制器中定义好的初始化方法，可以做一些具体操作之前的初始化工作。
     * 我们在这里进行标题的统一配置和数据传输。
     */
    protected function _initialize() {
        trace(ACTION_NAME);
        $meta_titles = array(
            'index' => '管理员管理',
            'add'   => '添加管理员',
            'edit'  => '修改管理员',
        );
        $meta_title  = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '管理员管理';
        $this->assign('meta_title', $meta_title);
    }

    private function before_edit_view() {
        //取出所有的角色
        $this->assign('all_roles', D('Role')->getList('id,name', array()));
        //取出所有的权限
        $this->assign('all_permissions', D('Permission')->getList('id,name,parent_id', array(), true));
    }

    /**
     * 管理员列表.
     * 具备分页功能.
     */
    public function index($keyword = '') {
//        $menus = session('MENUS');
//        var_dump($menus);exit;
        //记录当前页面地址，为编辑跳转做准备
        cookie('forward', __SELF__);

        //1.创建模型
        $model           = D('Admin');
        //2.获取数据
        $where['status'] = array('egt', 0);
        if ($keyword) {
            $where['name'] = array('like', $keyword . '%');
        }
        $data = $model->getList('*', $where); //获取分页代码，和当前页内容
        //3.展示数据
        $this->assign($data);
        $this->assign('keyword', $keyword);
        $this->display();
    }

    /**
     * 添加管理员
     */
    public function add() {
        //1.判断是否是post提交
        if (IS_POST) {
            $model = D('Admin');
            //1.1判断数据是否合法create
            if ($model->create()) {
                //1.2插入数据
                if ($model->addAdmin() !== false) {
                    $this->success('插入成功', U('index'));
                } else {
                    $this->error('插入失败');
                }
            } else {
                $this->error(get_errors($model->getError()));
            }
        } else {
            //2.如果不是就展示
            $this->before_edit_view();
            $this->display('edit');
        }
    }

    /**
     * 修改管理员
     */
    public function edit($id) {
        $model = D('Admin');
        if (IS_POST) {
            if ($model->create()) {
                if ($model->saveAdmin() !== false) {
                    $this->success('修改成功', cookie('forward'));
                } else {
                    $this->error('修改失败');
                }
            } else {
                $this->error(get_errors($model->getError()));
            }
        } else {
            //1.根据id获取数据表中的数据
            $this->before_edit_view();
            //获取所有的额外权限
            
            //获取当前用户关联的角色
            $row = $model->find($id);
            $this->assign('row', $row);
            $this->assign('perms', $model->getPermission($id,true));
            $this->assign('roles', $model->getRole($id,true));
            $this->display();
        }
    }

    /**
     * 修改供应商的状态，删除也是修改状态，所以可以都使用
     * @param type $id
     * @param type $status
     */
    public function changeStatus($id, $status = -1) {
        $model = D('Admin');
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
    
    /**
     * 如果验证通过，就跳转到后台首页
     * 如果验证不通过，就返回前页
     */
    public function login(){
        if(IS_POST){
            //判断验证码是否正确，如果验证码正确，就返回，没有绝对的要求我们也可以写在模型中
            $username = I('post.username');
            $password = I('post.password');
            $model = D('Admin');
            if($model->login($username,$password)){
                $this->success('登录成功',U('Index/index'));
            } else{
                $this->error($model->getError());
            }
        }else{
            $this->display();
        }
    }
    
    /**
     * 删除token：cookie和数据库中
     */
    public function logout(){
        D('AdminToken')->deleteToken(cookie('admin_id'));
        session(null);
        cookie(null);
        $this->success('退出成功',U('login'));
    }

}
