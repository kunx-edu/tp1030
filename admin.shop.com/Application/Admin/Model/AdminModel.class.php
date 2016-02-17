<?php

namespace Admin\Model;

/**
 *
 * @author kunx
 */
class AdminModel extends \Think\Model {

    protected $patchValidate = true; //开启批量验证
    protected $_validate     = array(
        array('username', '', '用户名不能重复', self::EXISTS_VALIDATE, 'unique'),
        array('username', 'require', '用户名不能为空'),
        array('password', 'require', '密码不能为空'),
        array('email', '', '邮箱不能重复', self::EXISTS_VALIDATE, 'unique'),
        array('email', 'require', '邮箱不能为空'),
        array('email', 'email', '邮箱不合法'),
        array('repassword', 'password', '两次密码不一致', self::EXISTS_VALIDATE, 'confirm'),
        array('password', '6,16', '密码长度不合法', self::EXISTS_VALIDATE, 'length'),
    );

    /**
     * TODO：自动完成
     */
    protected $_auto = array(
        array('salt','\Org\Util\String::randString',self::MODEL_INSERT,'function',array(6)),
        array('add_time',NOW_TIME,self::MODEL_INSERT),
//        array('last_login_time',NOW_TIME,self::MODEL_UPDATE),
//        array('last_login_ip','get_client_ip',self::MODEL_UPDATE,'function',array(1)),
//        array('password','kunx_password',self::MODEL_BOTH,'callback'),
    );
    
//    protected function kunx_password(){
//        var_dump($this->data);
//        exit;
//    }




    /**
     * @param int|array $id
     * @param int $status
     */
    public function changeStatus($id, $status = -1) {
        //如果id不是数组，就转换成数组，以便后面使用统一的tp的where数组格式。
        if (!is_array($id)) {
            $id = array($id);
        }
        //1.如果status是-1，那么就将原来的名字后添加_del
        if ($status == -1) {
            $data['name'] = array('exp', 'concat(name,"_del")');
        }
        $data['status'] = $status;
        //2.执行数据的更新操作。
        return D('Brand')->where(array('id' => array('in', $id)))->setField($data);
    }

    /**
     * 添加管理员
     * @return boolean
     */
    public function addAdmin() {
        unset($this->data['id']);
        $this->data['password'] = my_mcrypt($this->data['password'], $this->data['salt']);
        //保存本表数据
        if(($id = $this->add()) === false){
            $this->error = '创建管理员失败';
            return false;
        }
        //保存用户-角色中间表
        if($this->roleHandler($id)===false){
            $this->error = '保存角色失败';
            return false;
        }
        
        //保存用户-权限中间表
        if($this->permissionHandler($id)===false){
            $this->error = '保存权限失败';
            return false;
        }
        return true;
    }
    
    /**
     * 添加管理员
     * @return boolean
     */
    public function saveAdmin() {
        //保存本表数据
        $id = $this->data['id'];
        if($this->save() === false){
            $this->error = '创建管理员失败';
            return false;
        }
        //保存用户-角色中间表
        M('AdminRole')->where(array('admin_id'=>$id))->delete();
        if($this->roleHandler($id)===false){
            $this->error = '保存角色失败';
            return false;
        }
        
        //保存用户-权限中间表
        M('AdminPermission')->where(array('admin_id'=>$id))->delete();
        if($this->permissionHandler($id)===false){
            $this->error = '保存权限失败';
            return false;
        }
        return true;
    }
    
    
    private function roleHandler($admin_id){
        $roles = I('post.role_ids');
        $data = array();
        foreach($roles as $role){
            $data[] = array(
                'admin_id'=>$admin_id,
                'role_id'=>$role,
            );
        }
        return M('AdminRole')->addAll($data);
    }
    private function permissionHandler($admin_id){
        $permission_ids = I('post.permission_ids');
        $data = array();
        foreach($permission_ids as $permission){
            $data[] = array(
                'admin_id'=>$admin_id,
                'permission_id'=>$permission,
            );
        }
        return M('AdminPermission')->addAll($data);
    }

    /**
     * 获取管理员列表
     * @param string $field
     * @param array $where
     * @return type
     */
    public function getList($field = '*', array $where = array()) {
        $tmp['status'] = array('egt', 0);
        $where         = array_merge($tmp, $where);
        //获取分页代码
        $count         = $this->where($where)->count();
        $size          = C('PAGE_SIZE') ? C('PAGE_SIZE') : 10;
        $page          = new \Think\Page($count, $size);
        $page->setConfig('theme', C('PAGE_THEME'));
        $page_html     = $page->show();
        //获取当前页数据
        $rows          = $this->field($field)->page(I(p,1),$size)->where($where)->select();
        return array('page_html'=>$page_html,'rows'=>$rows);
    }

    
    /**
     * 获取指定管理员所拥有的权限列表。
     * @param integer $admin_id
     * @param boolean $is_ajax
     * @return type
     */
    public function getPermission($admin_id,$is_ajax=false){
        $rows = M('AdminPermission')->where(array('admin_id'=>$admin_id))->select();
        if($is_ajax){
            return json_encode($rows);
        }else{
            return $rows;
        }
    }
    /**
     * 获取指定管理员所拥有的角色列表。
     * @param integer $admin_id
     * @param boolean $is_ajax
     * @return type
     */
    public function getRole($admin_id,$is_ajax=false){
        $rows = M('AdminRole')->field('role_id')->where(array('admin_id'=>$admin_id))->select();
        $rows = get_data_column($rows,'role_id');
        if($is_ajax){
            return json_encode($rows);
        }else{
            return $rows;
        }
    }
    
    /**
     * 用户登录。
     * 验证验证码
     * 验证用户名
     * 验证密码
     * @param string $username 用户名
     * @param string $password 密码
     * @return boolean true成功 false失败
     */
    public function login($username,$password){
        //判断验证码是否匹配
        $code = I('post.captcha');
        $captcha = new \Think\Verify();
        if($captcha->check($code)===false){
            $this->error = '验证码不正确';
            return false;
        }
        //1.根据用户名获取对应的记录，得到盐和密码
        $row = $this->getByUsername($username);
        if($row){
            //2.有记录，说明用户名是存在的，进行密码验证
            $salt = $row['salt'];
            $db_password = $row['password'];
            //3.使用加盐加密进行验证
            if(my_mcrypt($password, $salt)==$db_password){
                session('USERINFO',$row);
                //是否需要保存登录信息
                if(I('post.remember')){
                    //保存token到数据库和cookie
                    cookie('admin_id',$row['id'],604800);//保存一周
                    $token = create_token();
                    cookie('token',$token,604800);//保存一周
                    D('AdminToken')->addToken($row['id'],$token);
                }
                return true;//验证通过，用户名和密码匹配
            }else{
                $this->error = '密码不正确';
                return false;
            }
        }else{
            $this->error = '用户名不存在';
            return false;
        }
    }
}
